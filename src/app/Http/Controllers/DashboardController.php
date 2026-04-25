<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

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
        ]);
    }
}
