<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    // カテゴリー一覧を表示
    public function index()
    {
        // カテゴリー一覧を表示できるかどうかを認可
        $this->authorize('viewAny', Category::class);

        // カテゴリ一覧を取得する
        $categories = auth()->user()->categories()
        ->where('type', 'expense')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('category.index', compact('categories'));
    }

    // カテゴリーを作成
    public function store(CategoryRequest $request)
    {
        // カテゴリーを作成できるかどうかを認可
        $this->authorize('create', Category::class);
        // リクエストのバリデーションを実行
        $validatedData = $request->validated();
        // ユーザーIDを設定する
        $validatedData['user_id'] = auth()->id();
        // カテゴリーを作成する
        Category::create($validatedData);

        // カテゴリーが作成されたことをフラッシュデータとしてセッションに保存する
        return redirect()->route('category.index')->with('success', 'カテゴリーが作成されました。');
    }

    // カテゴリーを更新
    public function update(CategoryRequest $request, Category $category)
    {
        // カテゴリーを更新できるかどうかを認可
        $this->authorize('update', $category);
        // リクエストのバリデーションを実行
        $validatedData = $request->validated();
        //カテゴリを更新する
        $category->update($validatedData);
        // カテゴリが更新されたことをフラッシュデータとしてセッションに保存する
        return redirect()->route('category.index')->with('updated', 'カテゴリーが更新されました。');
    }

    //カテゴリーを削除
    public function destroy(Category $category)
    {
        //　カテゴリを削除できるかどうかを認可
        $this->authorize('delete', $category);
        // カテゴリを削除
        $category->delete();
        // カテゴリが削除されたことをフラッシュデータとしてセッションに保存する
        return redirect()->route('category.index')->with('deleted', 'カテゴリーが削除されました。');
    }
}
