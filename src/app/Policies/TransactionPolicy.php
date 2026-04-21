<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    // トランザクション一覧を表示できるかどうか
    public function viewAny(User $user): bool
    {
        return true;
    }

    // トランザクションを表示できるかどうか
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    // トランザクションを作成できるかどうか
    public function create(User $user): bool
    {
        return true;
    }

    // トランザクションを更新できるかどうか
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    // トランザクションを削除できるかどうか
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    // トランザクションを復元できるかどうか
    public function restore(User $user, Transaction $transaction): bool
    {
        return false;
    }

    // トランザクションを強制的に削除できるかどうか
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return false;
    }
}
