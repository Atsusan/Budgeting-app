@extends('layouts.app')

@section('title', 'カテゴリ設定')

@push('css')
<style>
    [x-cloak] {
        display: none !important;
    }

    /* カラーピッカーの見た目を少し整える */
    input[type="color"]::-webkit-color-swatch-wrapper {
        padding: 0;
    }

    input[type="color"]::-webkit-color-swatch {
        border: none;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')


{{-- 作成・更新・削除成功メッセージ --}}
@php
    // セッションキーに応じて色とアイコンを定義
    $config = match(true) {
        session()->has('success') => [
            'color' => 'text-green-500 bg-green-100',
            'border' => 'border-green-200',
            'msg' => session('success')
        ],
        session()->has('updated') => [
            'color' => 'text-blue-500 bg-blue-100',
            'border' => 'border-blue-200',
            'msg' => session('updated')
        ],
        session()->has('deleted') => [
            'color' => 'text-red-500 bg-red-100',
            'border' => 'border-red-200',
            'msg' => session('deleted')
        ],
        default => null
    };
@endphp

@if ($config)
<div x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-[-20px]"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-5 right-5 z-50 flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-xl border {{ $config['border'] }}"
    role="alert">

    {{-- アイコン部分の色を動的に変更 --}}
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg {{ $config['color'] }}">
        @if(session('deleted'))
            {{-- 削除時はゴミ箱アイコンなど --}}
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        @else
            {{-- 作成・更新時はチェックアイコン --}}
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
        @endif
    </div>

    <div class="ml-3 text-sm font-medium">{{ $config['msg'] }}</div>
</div>
@endif

<div x-data="{
    showModal: false,
    editMode: false,
    categoryForm: { id: null, name: '', color_code: '#6366f1' },
    baseUrl: '{{ url('category') }}',

    openCreateModal() {
        this.editMode = false;
        this.categoryForm = { id: null, name: '', color_code: '#6366f1' };
        this.showModal = true;
    },
    openEditModal(category) {
        this.editMode = true;
        this.categoryForm = { ...category };
        this.showModal = true;
    },
    hydrateFromOldInput() {
        @if ($errors->any())
            this.showModal = true;
            this.editMode = {{ old('_method') === 'PATCH' ? 'true' : 'false' }};
            this.categoryForm.id = '{{ old('category_id') }}';
            this.categoryForm.name = '{{ old('name') }}';
            this.categoryForm.color_code = '{{ old('color_code', '#6366f1') }}';
        @endif
    }
}" x-init="hydrateFromOldInput()" class="max-w-2xl mx-auto py-10 px-4">

    {{-- ヘッダーセクション --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="group p-2 bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-100 rounded-xl transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">カテゴリ設定</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Manage Categories</p>
            </div>
        </div>
        <button @click="openCreateModal()" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            新規追加
        </button>
    </div>

    {{-- リストセクション --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="pl-8 pr-4 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Color</th>
                    <th class="px-4 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Category Name</th>
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">Settings</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories as $category)
                <tr class="hover:bg-indigo-50/30 transition-colors group">
                    <td class="pl-8 pr-4 py-5">
                        <div class="w-10 h-3 rounded-full shadow-sm" style="background-color: {{ $category->color_code }}"></div>
                    </td>
                    <td class="px-4 py-5">
                        <span class="font-bold text-gray-700">{{ $category->name }}</span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button
                            type="button"
                            @click="openEditModal(@js($category->only(['id', 'name', 'color_code'])))"
                            class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-indigo-600 bg-gray-100 hover:bg-indigo-100 px-4 py-2 rounded-xl transition-all">
                            編集する
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-8 py-12 text-center text-gray-400 font-medium">
                        カテゴリがまだ登録されていません
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- モーダル --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-6" x-cloak>
        <div x-show="showModal" x-transition.opacity @click="showModal = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-md"></div>
        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            class="bg-white rounded-[3rem] shadow-2xl max-w-md w-full relative z-10 overflow-hidden">
            <div class="p-10">
                <div class="mb-8">
                    <h3 class="text-2xl font-black text-gray-800" x-text="editMode ? 'カテゴリを編集' : '新しいカテゴリ'"></h3>
                    <p class="text-sm text-gray-400 font-medium mt-1" x-text="editMode ? '内容を更新または削除できます' : '識別しやすい名前と色を設定しましょう'"></p>
                </div>
                <form :action="editMode ? `${baseUrl}/${categoryForm.id}` : '{{ route('category.store') }}'" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PATCH">
                    </template>
                    <input type="hidden" name="category_id" x-model="categoryForm.id">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1 mb-2">カテゴリ名</label>
                            <input type="text" name="name" x-model="categoryForm.name" required placeholder="例：食費、エンタメなど"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl font-bold text-gray-700 outline-none transition-all">
                            @error('name') <p class="text-red-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest ml-1 mb-2">ラベルカラー</label>
                            <div class="flex items-center gap-4 bg-gray-50 p-3 rounded-2xl">
                                <input type="color" name="color_code" x-model="categoryForm.color_code"
                                    class="w-16 h-12 rounded-xl cursor-pointer bg-transparent border-none">
                                <span class="font-mono font-bold text-gray-400 uppercase" x-text="categoryForm.color_code"></span>
                            </div>
                            @error('color_code') <p class="text-red-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <input type="hidden" name="type" value="expense">
                        <div class="mt-10 grid grid-cols-1 gap-3">
                            <button type="submit" class="w-full bg-indigo-600 text-white py-4 font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                保存する
                            </button>
                            <div class="flex gap-3">
                                <template x-if="editMode">
                                    <button type="button"
                                        @click="if(confirm('このカテゴリを削除してもよろしいですか？')) {
                                        const form = $el.closest('form');
                                        form.action = `${baseUrl}/${categoryForm.id}`;
                                        form.querySelector('[name=_method]').value = 'DELETE';
                                        form.submit();
                                    }"
                                        class="flex-1 py-4 text-red-500 font-bold hover:bg-red-50 rounded-2xl transition-all">
                                        削除
                                    </button>
                                </template>
                                <button type="button" @click="showModal = false" :class="editMode ? 'flex-1' : 'w-full'" class="py-4 text-gray-400 font-bold hover:bg-gray-50 rounded-2xl transition-all">
                                    キャンセル
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
