<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'color_code',
    ];

    // 所有者のユーザーを取得
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリーが持つトランザクションを取得
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
