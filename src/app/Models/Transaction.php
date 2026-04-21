<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'description',
        'amount',
        'date',
    ];

    // 日付をdate型にキャスト
    protected $casts = [
        'date' => 'date',
    ];

    // 所有者のユーザーを取得
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 所属するカテゴリーを取得
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
