<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class TransactionService
{
    public function store(array $rows): void
    {
        // 失敗した時に全件ロールバック
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row)  {
                Transaction::create([
                    'user_id' => auth()->id(),
                    'date' => $row['date'],
                    'category_id' => $row['category_id'],
                    'description' => $row['description'],
                    'amount' => $row['amount'],
                ]);
            }
        });
    }
}
