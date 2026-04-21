<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    // カテゴリー一覧を表示できるかどうか
    public function viewAny(User $user): bool
    {
        return true;
    }

    // カテゴリーを表示できるかどうか
    public function view(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    // カテゴリーを作成できるかどうか
    public function create(User $user): bool
    {
        return true;
    }

    // カテゴリーを更新できるかどうか
    public function update(User $user, Category $category): bool
    {
        return $user->id === $category->user_id
            && $category->type === 'expense';
    }

    // カテゴリーを削除できるかどうか
    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id
            && $category->type === 'expense';
    }

    // カテゴリーを復元できるかどうか
    public function restore(User $user, Category $category): bool
    {
        return false;
    }

    // カテゴリーを強制的に削除できるかどうか
    public function forceDelete(User $user, Category $category): bool
    {
        return false;
    }
}
