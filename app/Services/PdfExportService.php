<?php

namespace App\Services;

use App\Models\Account;
use App\Services\ReportService;
use App\Support\ReportPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class PdfExportService
{
    public function __construct(
        private ReportService $reportService
    ) {
    }

    public function exportMonthlySummary(int $accountId, int $month, int $year)
    {
        $account = Account::findOrFail($accountId);
        $timezone = $account->timezone ?? 'UTC';
        $period = ReportPeriod::forMonth($month, $year, $timezone);
        
        $summary = $this->reportService->getMonthlySummary($accountId, $month, $year, $timezone);
        $categoryBreakdown = $this->reportService->getCategoryBreakdown(
            $accountId, 
            $period->startDateOnly(), 
            $period->endDateOnly(), 
            $timezone
        );
        
        $transactions = \App\Models\Transaction::forAccount($accountId)
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->with(['wallet', 'category', 'creator'])
            ->orderBy('occurred_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.monthly-summary', [
            'account' => $account,
            'summary' => $summary,
            'categoryBreakdown' => $categoryBreakdown,
            'transactions' => $transactions,
            'month' => $month,
            'year' => $year,
        ]);

        return $pdf;
    }

    public function exportDateRangeStatement(int $accountId, string $startDate, string $endDate)
    {
        $account = Account::findOrFail($accountId);
        $timezone = $account->timezone ?? 'UTC';
        $period = ReportPeriod::fromDates($startDate, $endDate, $timezone);
        
        $income = \App\Models\Transaction::forAccount($accountId)
            ->byType('income')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->sum('amount');

        $expense = \App\Models\Transaction::forAccount($accountId)
            ->byType('expense')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->sum('amount');

        $summary = [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'net' => (float) $income - (float) $expense,
        ];
        
        $categoryBreakdown = $this->reportService->getCategoryBreakdown($accountId, $startDate, $endDate, $timezone);
        
        $transactions = \App\Models\Transaction::forAccount($accountId)
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->with(['wallet', 'category', 'creator'])
            ->orderBy('occurred_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.statement', [
            'account' => $account,
            'summary' => $summary,
            'categoryBreakdown' => $categoryBreakdown,
            'transactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf;
    }
}
