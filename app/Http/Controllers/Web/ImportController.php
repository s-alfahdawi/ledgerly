<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\AccountContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index()
    {
        $accountId = $this->requireAccountId();

        $wallets = Wallet::forAccount($accountId)->active()->get();
        $categories = Category::forAccount($accountId)->active()->get();

        return view('import.index', compact('wallets', 'categories'));
    }

    public function preview(Request $request)
    {
        $accountId = $this->requireAccountId();

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $rows = [];
        $headers = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $lineNum = 0;
            while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false && $lineNum < 101) {
                if ($lineNum === 0) {
                    $headers = $data;
                } else {
                    $rows[] = $data;
                }
                $lineNum++;
            }
            fclose($handle);
        }

        if (empty($headers) || empty($rows)) {
            return back()->with('error', 'CSV file is empty or could not be parsed.');
        }

        session(['import_headers' => $headers, 'import_rows' => $rows]);

        $wallets = Wallet::forAccount($accountId)->active()->get();
        $categories = Category::forAccount($accountId)->active()->get();

        return view('import.preview', compact('headers', 'rows', 'wallets', 'categories'));
    }

    public function process(Request $request)
    {
        $accountId = $this->requireAccountId();

        $request->validate([
            'date_column' => ['required', 'integer', 'min:0'],
            'amount_column' => ['required', 'integer', 'min:0'],
            'note_column' => ['nullable', 'integer', 'min:0'],
            'type_column' => ['nullable', 'integer', 'min:0'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'default_category_id' => ['nullable', 'exists:categories,id'],
            'default_type' => ['required', 'in:income,expense,auto'],
        ]);

        $headers = session('import_headers');
        $rows = session('import_rows');

        if (!$headers || !$rows) {
            return redirect()->route('import.index')->with('error', 'Import session expired. Please upload the file again.');
        }

        Wallet::forAccount($accountId)->findOrFail($request->wallet_id);

        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use ($request, $rows, $accountId, &$imported, &$skipped) {
            foreach ($rows as $row) {
                try {
                    $dateRaw = trim($row[$request->date_column] ?? '');
                    $amountRaw = trim($row[$request->amount_column] ?? '');
                    $noteRaw = $request->note_column !== null ? trim($row[$request->note_column] ?? '') : '';

                    $amount = (float) preg_replace('/[^0-9.\-]/', '', $amountRaw);
                    if ($amount == 0) {
                        $skipped++;
                        continue;
                    }

                    $type = $request->default_type;
                    if ($type === 'auto') {
                        $type = $amount < 0 ? 'expense' : 'income';
                        $amount = abs($amount);
                    } elseif ($request->type_column !== null) {
                        $typeRaw = strtolower(trim($row[$request->type_column] ?? ''));
                        if (in_array($typeRaw, ['income', 'expense'])) {
                            $type = $typeRaw;
                        }
                    }

                    $date = $this->parseDate($dateRaw);
                    if (!$date) {
                        $skipped++;
                        continue;
                    }

                    Transaction::create([
                        'account_id' => $accountId,
                        'wallet_id' => $request->wallet_id,
                        'category_id' => $request->default_category_id ?: null,
                        'type' => $type,
                        'amount' => abs($amount),
                        'occurred_at' => $date,
                        'note' => $noteRaw ?: 'Imported',
                        'created_by' => auth()->id(),
                    ]);
                    $imported++;
                } catch (\Throwable $e) {
                    $skipped++;
                }
            }
        });

        session()->forget(['import_headers', 'import_rows']);

        return redirect()->route('transactions.index')
            ->with('success', "Import complete: {$imported} transactions imported, {$skipped} skipped.");
    }

    private function parseDate(string $dateRaw): ?string
    {
        $formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'Y/m/d', 'm-d-Y', 'd-m-Y', 'Y-m-d H:i:s', 'Y-m-d H:i'];

        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $dateRaw);
            if ($parsed && $parsed->format($format) === $dateRaw) {
                return $parsed->format('Y-m-d H:i:s');
            }
        }

        $timestamp = strtotime($dateRaw);
        if ($timestamp !== false) {
            return date('Y-m-d H:i:s', $timestamp);
        }

        return null;
    }
}
