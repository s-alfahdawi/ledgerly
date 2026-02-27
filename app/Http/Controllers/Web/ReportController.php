<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\AccountContext;
use App\Services\PdfExportService;
use App\Services\ReportService;
use App\Support\ReportPeriod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private AccountContext $accountContext,
        private ReportService $reportService,
        private PdfExportService $pdfExportService
    ) {
    }

    public function index(Request $request)
    {
        $accountId = $this->requireAccountId();

        $account = $this->accountContext->account();
        $timezone = $account->timezone ?? 'UTC';
        $now = now($timezone);

        $startDate = $request->get('start_date', $now->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', $now->endOfMonth()->toDateString());
        $transactionType = $request->get('transaction_type');

        $summary = $this->reportService->getMonthlySummary($accountId, $now->month, $now->year, $timezone);

        $categoryBreakdown = $this->reportService->getCategoryBreakdown($accountId, $startDate, $endDate, $timezone, $transactionType);
        $walletBreakdown = $this->reportService->getWalletBreakdown($accountId, $startDate, $endDate, $timezone, $transactionType);

        return view('reports.index', compact('summary', 'categoryBreakdown', 'walletBreakdown', 'startDate', 'endDate', 'transactionType'));
    }

    public function exportMonthly(Request $request)
    {
        $accountId = $this->requireAccountId();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $pdf = $this->pdfExportService->exportMonthlySummary($accountId, $month, $year);

        return $pdf->download("monthly-summary-{$year}-{$month}.pdf");
    }

    public function exportStatement(Request $request)
    {
        $accountId = $this->requireAccountId();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $pdf = $this->pdfExportService->exportDateRangeStatement($accountId, $startDate, $endDate);

        return $pdf->download("statement-{$startDate}-to-{$endDate}.pdf");
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $accountId = $this->requireAccountId();

        $account = $this->accountContext->account();
        $timezone = $account->timezone ?? 'UTC';
        $startDate = $request->get('start_date', now($timezone)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now($timezone)->endOfMonth()->toDateString());
        $transactionType = $request->get('transaction_type');

        $period = ReportPeriod::fromDates($startDate, $endDate, $timezone);

        $query = Transaction::forAccount($accountId)
            ->with(['wallet', 'category', 'creator'])
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->orderBy('occurred_at', 'desc');

        if ($transactionType) {
            $query->byType($transactionType);
        }

        $transactions = $query->get();
        $filename = "transactions-{$startDate}-to-{$endDate}.csv";

        return response()->streamDownload(function () use ($transactions, $account) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Type', 'Amount', 'Currency', 'Wallet', 'Category', 'Note', 'Created By']);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->occurred_at->format('Y-m-d H:i'),
                    ucfirst($t->type),
                    number_format((float) $t->amount, 2, '.', ''),
                    $account->currency_code,
                    $t->wallet?->name ?? '',
                    $t->category?->name ?? '',
                    $t->note ?? '',
                    $t->creator?->name ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
