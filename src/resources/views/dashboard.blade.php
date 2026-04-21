@extends('layouts.app')

@section('title', '家計簿ダッシュボード')

@push('css')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@section('content')
{{-- Alpine.js の状態管理 --}}
<div x-data="{
    isOpen: false,
    selectedItem: {},
    openModal(item) {
        this.selectedItem = item;
        this.isOpen = true;
    }
}" @keydown.escape.window="isOpen = false" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{ session('success') }}
    @if (session('success'))
        <div class="fixed top-5 right-5 z-50 flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-xl border border-green-200">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-green-500 bg-green-100">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
            </div>
            <div class="ml-3 text-sm font-medium">{{ session('success') }}</div>
        </div>
    @endif
    {{-- ヘッダーセクション --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <button class="p-2 hover:bg-gray-100 rounded-full transition text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <h1 class="text-2xl font-bold text-gray-800">2026年3月</h1>
            <button class="p-2 hover:bg-gray-100 rounded-full transition text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('category.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 text-sm font-medium rounded-xl text-gray-600 bg-white hover:bg-gray-50 transition-all focus:outline-none">
                カテゴリ管理
            </a>
            <a href="{{ route('transaction.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition-all transform hover:scale-105 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                収支を入力する
            </a>
        </div>
    </div>

    {{-- サマリーカード --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500 mb-1">今月の総支出</p>
            <p class="text-3xl font-extrabold text-red-500">¥84,200</p>
            <p class="text-xs text-gray-400 mt-2">先月より ¥12,000 減少</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500 mb-1">今月の総収入</p>
            <p class="text-3xl font-extrabold text-emerald-500">¥1,250,000</p>
            <p class="text-xs text-gray-400 mt-2">先月より ¥10,000 増加</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 bg-gradient-to-br from-white to-indigo-50/30">
            <p class="text-sm font-medium text-gray-500 mb-1">今月の収支残高</p>
            <p class="text-3xl font-extrabold text-indigo-900">¥165,800</p>
        </div>
    </div>

    {{-- メインレイアウト --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-gray-700">

        {{-- 左：履歴テーブル --}}
        <div class="lg:col-span-2">
            <h2 class="text-lg font-bold mb-4 flex items-center">
                <span class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></span>
                今月の収支履歴
            </h2>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">日付</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">内容</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">金額</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        {{-- 行をクリックで詳細モーダルを表示 --}}
                        @forelse ($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition cursor-pointer"
                                @click="openModal({
                                            id: '{{ $transaction->id }}',
                                            date: '{{ $transaction->date }}',
                                            category: '{{ $transaction->category->name }}',
                                            amount: '{{ $transaction->category->type === 'income' ? '+' : '-' }} ¥{{ number_format($transaction->amount) }}',
                                            type: '{{ $transaction->category->type }}',
                                            description: '{{ $transaction->description }}',
                                            color_code: '{{ $transaction->category->color_code }}'
                                        })">
                                <td class="px-6 py-4 text-gray-500">{{ $transaction->date->format('Y/m/d') }}</td>
                                <td class="px-6 py-4 font-medium">{{ $transaction->description }}</td>
                                <td class="px-6 py-4 font-bold {{ $transaction->category->type === 'income' ? 'text-emerald-500' : 'text-red-500' }}  text-right">
                                    {{ $transaction->category->type === 'income' ? '+' : '-' }} ¥{{ number_format($transaction->amount) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                    今月の収支履歴がありません
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 右：カテゴリ分析 --}}
        <div>
            <h2 class="text-lg font-bold mb-4 flex items-center">
                <span class="w-1.5 h-6 bg-orange-400 rounded-full mr-3"></span>
                支出カテゴリ分析
            </h2>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5">
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600 font-medium">食費</span>
                        <span class="font-bold">45%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-orange-400 h-2 rounded-full" style="width: 45%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600 font-medium">固定費</span>
                        <span class="font-bold">35%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-400 h-2 rounded-full" style="width: 35%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 詳細表示モーダル --}}
    <div x-show="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div x-show="isOpen" x-transition.opacity @click="isOpen = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div x-show="isOpen" x-transition.scale.95 class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden relative z-10">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">履歴詳細</h3>
                    <button @click="isOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 mb-8 text-sm">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">日付</span>
                        <span class="font-semibold text-gray-800" x-text="selectedItem.date"></span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">項目</span>
                        <span class="font-semibold text-gray-800" x-text="selectedItem.category"></span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">金額</span>
                        <span :class="selectedItem.type === 'income' ? 'text-emerald-600' : 'text-red-500'" class="text-xl font-black" x-text="selectedItem.amount"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <form class="w-full" :action="`/transaction/${selectedItem.id}`" method="POST">
                        @method('PATCH')
                        @csrf
                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 font-bold rounded-2xl hover:bg-indigo-700 transition">編集</button>
                    </form>
                    <form class="w-full" :action="`/transaction/${selectedItem.id}`" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="w-full bg-red-50 text-red-600 py-3 font-bold rounded-2xl hover:bg-red-100 transition">削除</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
