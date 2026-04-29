<?php

namespace App\Services;

use Carbon\Carbon;

class DashboardService
{
    public function getSummary(int $year, int $month): array
    {
        // 年月を指定してCarbonインスタンスを作成
        $targetMonth = $month
            ? Carbon::createFromDate($year, $month, 1)
            : now()->startOfMonth();

        // 一覧表示用の開始日と終了日を取得
        $startOfMonth = $targetMonth->copy()->startOfMonth()->toDateString();
        $endOfMonth = $targetMonth->copy()->endOfMonth()->toDateString();

        // トランザクションを取得
        $transactions = auth()->user()->transactions()
            ->with('category')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // カテゴリを取得
        $categories = auth()->user()->categories()
            ->select('id', 'name', 'type', 'color_code')
            ->orderBy('created_at')
            ->get();

        // 総支出、総収入、収支残高を計算
        $totalExpense = $transactions->filter(fn($t) => $t->category->type === 'expense')->sum('amount');
        $totalIncome = $transactions->filter(fn($t) => $t->category->type === 'income')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        // 先月の総支出、総収入、収支残高を計算
        $prevMonthStart = $targetMonth->copy()->subMonth()->startOfMonth()->toDateString();
        $prevMonthEnd = $targetMonth->copy()->subMonth()->endOfMonth()->toDateString();
        $prevTransactions = auth()->user()->transactions()
            ->with('category')
            ->whereBetween('date', [$prevMonthStart, $prevMonthEnd])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 先月の総支出、総収入、収支残高を計算
        $prevTotalExpense = $prevTransactions->filter(fn($t) => $t->category->type === 'expense')->sum('amount');
        $prevTotalIncome = $prevTransactions->filter(fn($t) => $t->category->type === 'income')->sum('amount');
        $expenseDiff = $totalExpense - $prevTotalExpense;
        $incomeDiff  = $totalIncome  - $prevTotalIncome;
        $balanceDiff = $totalBalance - ($prevTotalIncome - $prevTotalExpense);

        // 今月のカテゴリ別の支出を計算
        $categoryExpenses = $transactions->filter(fn($t) => $t->category->type === 'expense')
            ->groupBy('category_id')
            ->map(function ($group) {
                return [
                    'category_id' => $group->first()->category_id,
                    'category_name' => $group->first()->category->name,
                    'amount' => $group->sum('amount'),
                    'color_code' => $group->first()->category->color_code,
                ];
            })
            ->sortByDesc('amount')
            ->values();

        return [
            'transactions' => $transactions,
            'categories' => $categories,
            'targetMonth' => $targetMonth,
            'totalExpense' => $totalExpense,
            'totalIncome' => $totalIncome,
            'totalBalance' => $totalBalance,
            'expenseDiff' => $expenseDiff,
            'incomeDiff' => $incomeDiff,
            'balanceDiff' => $balanceDiff,
            'categoryExpenses' => $categoryExpenses,
        ];
    }
}
