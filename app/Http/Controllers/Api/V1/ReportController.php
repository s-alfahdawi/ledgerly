<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AccountContext;
use App\Services\ReportService;
use Illuminate\Http\Request;

/**
 * ReportController (API)
 * 
 * API endpoints for reports.
 * IMPORTANT: This controller only uses services - no web-specific logic.
 */
class ReportController extends Controller
{
    public function __construct(
        private AccountContext $accountContext,
        private ReportService $reportService
    ) {
    }

    public function monthlySummary(Request $request)
    {
        $accountId = $this->accountContext->id();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $summary = $this->reportService->getMonthlySummary($accountId, $month, $year);
        return response()->json($summary);
    }

    public function categoryBreakdown(Request $request)
    {
        $accountId = $this->accountContext->id();
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $breakdown = $this->reportService->getCategoryBreakdown($accountId, $startDate, $endDate);
        return response()->json($breakdown);
    }

    public function walletBreakdown(Request $request)
    {
        $accountId = $this->accountContext->id();
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $breakdown = $this->reportService->getWalletBreakdown($accountId, $startDate, $endDate);
        return response()->json($breakdown);
    }
}
