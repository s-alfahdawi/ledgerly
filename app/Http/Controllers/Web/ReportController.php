<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AccountContext;
use App\Services\PdfExportService;
use App\Services\ReportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private AccountContext $accountContext,
        private ReportService $reportService,
        private PdfExportService $pdfExportService
    ) {
    }

    public function index(Request $request)
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        
        $account = $this->accountContext->account();
        $timezone = $account->timezone ?? 'UTC';
        $now = now($timezone);
        
        $startDate = $request->get('start_date', $now->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', $now->endOfMonth()->toDateString());
        $transactionType = $request->get('transaction_type'); // 'income', 'expense', or null

        $summary = $this->reportService->getMonthlySummary($accountId, $now->month, $now->year, $timezone);

        $categoryBreakdown = $this->reportService->getCategoryBreakdown($accountId, $startDate, $endDate, $timezone, $transactionType);
        $walletBreakdown = $this->reportService->getWalletBreakdown($accountId, $startDate, $endDate, $timezone, $transactionType);

        return view('reports.index', compact('summary', 'categoryBreakdown', 'walletBreakdown', 'startDate', 'endDate', 'transactionType'));
    }

    public function exportMonthly(Request $request)
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $pdf = $this->pdfExportService->exportMonthlySummary($accountId, $month, $year);

        return $pdf->download("monthly-summary-{$year}-{$month}.pdf");
    }

    public function exportStatement(Request $request)
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $pdf = $this->pdfExportService->exportDateRangeStatement($accountId, $startDate, $endDate);

        return $pdf->download("statement-{$startDate}-to-{$endDate}.pdf");
    }
}
