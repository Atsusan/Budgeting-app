@extends('layouts.app')

@section('title', '一括入力')

@push('css')
<style>
    [x-cloak] { display: none !important; }
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.4;
    }

    .btn-save-expense {
        background-color: #4f46e5 !important;
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2) !important;
    }
    .btn-save-expense:hover { background-color: #4338ca !important; }

    .btn-save-income {
        background-color: #10b981 !important;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2) !important;
    }
    .btn-save-income:hover { background-color: #059669 !important; }
</style>
@endpush

@section('content')

<script>
    const allCategories = {{ Js::from($categories) }}
    const expenseCategories = allCategories.filter(c => c.type === 'expense');
    const incomeCategories = allCategories.filter(c => c.type === 'income');
</script>

<div x-data="{
    activeTab: 'expense',
    {{-- ★1. 最初は空の配列にする --}}
    rows: [],
    expenseCategories: expenseCategories,
    incomeCategories: incomeCategories,

    get currentCategories() {
        return this.activeTab === 'expense' ? this.expenseCategories : this.incomeCategories;
    },

    {{-- ★2. 初期化時に1行目を追加する処理 --}}
    init() {
        this.addRow();
    },

    addRow() {
        const defaultCat = this.currentCategories[0].id;
        this.rows.push({ id: Date.now(), date: '{{ date('Y-m-d') }}', category_id: defaultCat, description: '', amount: '' });
    },
    removeRow(index) {
        {{-- ★3. 1個もなくなったら困るので0ではなく1を最小にするか、表示を工夫する --}}
        this.rows.splice(index, 1);
        if (this.rows.length === 0) this.addRow();
    },
    switchTab(tab) {
        if (confirm('入力中の内容はリセットされます。よろしいですか？')) {
            this.activeTab = tab;
            this.rows = [];
            this.addRow();
        }
    }
}" class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8" x-cloak>

    {{-- ヘッダーセクション --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="group p-2 bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-100 rounded-xl transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">収支一括入力</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Bulk Entry</p>
            </div>
        </div>

        <div class="inline-flex p-1.5 bg-gray-100 rounded-xl shadow-inner">
            <button @click="switchTab('expense')"
                :class="activeTab === 'expense' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all">支出</button>
            <button @click="switchTab('income')"
                :class="activeTab === 'income' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all">収入</button>
        </div>
    </div>

    @if ($errors->any())
    <div class="mb-4">
        <ul class="text-sm text-red-600 flex flex-col gap-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- メイン入力エリア --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="pl-8 pr-4 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">日付</th>
                                <th class="px-4 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">カテゴリ</th>
                                <th class="px-4 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">内容</th>
                                <th class="px-4 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">金額</th>
                                <th class="pl-4 pr-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="(row, index) in rows" :key="row.id">
                                <tr class="transition-colors group">
                                    <td class="pl-8 pr-4 py-5">
                                        <input type="date" x-model="row.date" class="w-full px-3 py-3 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-xl font-bold text-gray-700 text-sm outline-none transition-all">
                                    </td>
                                    <td class="px-2 py-4">
                                        <select x-model="row.category_id" class="w-full px-3 py-3 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-xl font-bold text-gray-700 text-sm outline-none transition-all cursor-pointer appearance-none">
                                            <template x-for="cat in currentCategories" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" x-model="row.description" placeholder="内容を入力" class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-xl font-bold text-gray-700 text-sm outline-none transition-all">
                                    </td>
                                    <td class="px-2 py-4">
                                        <div class="relative">
                                            <input type="number"
                                                x-model="row.amount"
                                                :id="'amount-' + row.id"
                                                placeholder="0"
                                                @keydown.enter.prevent="
                                                    addRow();
                                                    if (!$event.shiftKey) {
                                                        $nextTick(() => {
                                                            document.getElementById('amount-' + rows[rows.length - 1].id)?.focus()
                                                        })
                                                    }
                                                "
                                                :class="activeTab === 'income' ? 'text-emerald-600' : 'text-gray-900'"
                                                class="w-full pl-7 pr-4 py-3 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-xl font-black text-right text-base outline-none transition-all">
                                        </div>
                                    </td>
                                    <td class="pl-2 pr-8 py-4">
                                        <button @click="removeRow(index)" class="p-2 text-gray-300 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="p-8 bg-gray-50/30">
                    <button @click="addRow()" class="w-full flex items-center justify-center gap-2 py-4 border-2 border-dashed border-gray-200 rounded-xl text-sm font-bold text-gray-400 hover:bg-white hover:border-indigo-400 hover:text-indigo-600 transition-all group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        新しい明細を追加する
                    </button>
                </div>
            </div>
        </div>

        {{-- サイド集計エリア --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-10 rounded-xl shadow-xl shadow-gray-200/50 border border-gray-100">
                <div class="mb-8">
                    <h3 class="text-xl font-black text-gray-800" x-text="activeTab === 'expense' ? '支出合計' : '収入合計'"></h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Manage Balance</p>
                </div>

                <div class="flex items-baseline mb-10">
                    <span class="text-xl font-bold text-gray-400 mr-1.5">¥</span>
                    <span class="text-4xl font-black tracking-tight"
                        :class="activeTab === 'income' ? 'text-emerald-600' : 'text-gray-800'"
                        x-text="rows.reduce((sum, row) => sum + (Number(row.amount) || 0), 0).toLocaleString()">
                    </span>
                </div>

                <div class="space-y-4 pt-8 border-t border-gray-50">
                    <div class="flex justify-between items-center px-1 text-gray-500">
                        <span class="text-xs font-black uppercase tracking-widest">Count</span>
                        <span class="font-bold text-gray-700" x-text="rows.filter(r => r.amount > 0).length + ' items'"></span>
                    </div>


                <form method="POST" action="{{ route('transaction.store') }}">
                    @csrf
                    <input type="hidden" name="rows" :value="JSON.stringify(rows)">
                    <button type="submit"
                        class="w-full py-5 rounded-xl font-black text-white transition-all active:scale-95 mt-4"
                        :class="activeTab === 'income' ? 'btn-save-income' : 'btn-save-expense'">
                        一括保存する
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
