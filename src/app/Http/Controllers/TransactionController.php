<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        // ユーザーを取得
        $user = auth()->user();
        // カテゴリーを取得
        $categories = $user->categories()
            ->select('id', 'name', 'type')
            ->orderBy('created_at')
            ->get();


        return view('transaction.index', [
            'categories'  => $categories,
        ]);
    }
    public function create()
    {

    }
    public function store(TransactionRequest $request)
    {
        $validated = $request->validated();

        // 失敗した時に全件ロールバック
        DB::transaction(function () use ($validated) {
            foreach ($validated['rows'] as $row)  {
                Transaction::create([
                    'user_id' => auth()->id(),
                    'date' => $row['date'],
                    'category_id' => $row['category_id'],
                    'description' => $row['description'],
                    'amount' => $row['amount'],
                ]);
            }
        });

        return redirect()->route('dashboard')->with('success', '取引が保存されました。');
    }
    public function edit(Transaction $transaction)
    {
        dd($transaction);
    }
    public function update(Request $request, Transaction $transaction)
    {
    }
    public function destroy(Transaction $transaction)
    {
    }
}
