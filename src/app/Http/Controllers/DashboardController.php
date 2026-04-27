<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 年月パラメータを取得
        $yearParam = request('year', Carbon::now()->year);
        $monthParam = request('month', Carbon::now()->month);
        // 年月を指定してCarbonインスタンスを作成
        $targetMonth = $monthParam
            ? Carbon::createFromFormat('Y-m', "{$yearParam}-{$monthParam}")
            : now()->startOfMonth();

        // 一覧表示用の開始日と終了日を取得
        $startOfMonth = $targetMonth->copy()->startOfMonth()->toDateString();
        $endOfMonth = $targetMonth->copy()->endOfMonth()->toDateString();

        $transactions = auth()->user()->transactions()
            ->with('category')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

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
        $prevTotalExpense = $prevTransactions->filter(fn($t) => $t->category->type === 'expense')->sum('amount');
        $prevTotalIncome = $prevTransactions->filter(fn($t) => $t->category->type === 'income')->sum('amount');
        $expenseDiff = $totalExpense - $prevTotalExpense;
        $incomeDiff  = $totalIncome  - $prevTotalIncome;
        $balanceDiff = $totalBalance - ($prevTotalIncome - $prevTotalExpense);
        return view('dashboard', [
            'transactions' => $transactions,
            'categories' => $categories,
            'targetMonth' => $targetMonth,
            'totalExpense' => $totalExpense,
            'totalIncome' => $totalIncome,
            'totalBalance' => $totalBalance,
            'expenseDiff' => $expenseDiff,
            'incomeDiff' => $incomeDiff,
            'balanceDiff' => $balanceDiff,
        ]);
    }
}
