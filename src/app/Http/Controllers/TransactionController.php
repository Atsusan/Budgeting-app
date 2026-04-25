<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
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
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        abort_if($transaction->user_id != auth()->id(), 403);
        $validated = $request->validated();
        $transaction->update($validated);

        return redirect()->route('dashboard')->with('updated', '取引が更新されました。');
    }
    public function destroy(Transaction $transaction)
    {
        abort_if($transaction->user_id != auth()->id(), 403);

        $transaction->delete();
        return redirect()->route('dashboard')->with('deleted', '取引が削除されました。');
    }
}
