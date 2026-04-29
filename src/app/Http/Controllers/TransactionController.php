<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;

class TransactionController extends Controller
{

    // コンストラクタ
    public function __construct(
        private TransactionService $transactionService
    ){}

    // 一覧画面
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

    // 作成画面
    public function create()
    {

    }

    // 作成
    public function store(TransactionRequest $request)
    {
        $validated = $request->validated();

        $this->transactionService->store($validated['rows']);

        return redirect()->route('dashboard')->with('success', '取引が保存されました。');
    }

    // 更新
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        abort_if($transaction->user_id != auth()->id(), 403);
        $validated = $request->validated();
        $transaction->update($validated);

        return redirect()->route('dashboard')->with('updated', '取引が更新されました。');
    }

    // 削除
    public function destroy(Transaction $transaction)
    {
        abort_if($transaction->user_id != auth()->id(), 403);

        $transaction->delete();
        return redirect()->route('dashboard')->with('deleted', '取引が削除されました。');
    }
}
