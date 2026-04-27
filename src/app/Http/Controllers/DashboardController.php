<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $yearParam = request('year', Carbon::now()->year);
        $monthParam = request('month', Carbon::now()->month);
        $targetMonth = $monthParam
            ? Carbon::createFromFormat('Y-m', "{$yearParam}-{$monthParam}")
            : now()->startOfMonth();

        // 一覧表示用
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

        return view('dashboard', [
            'transactions' => $transactions,
            'categories' => $categories,
            'targetMonth' => $targetMonth,
        ]);
    }
}
