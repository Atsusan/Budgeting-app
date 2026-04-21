<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class DefaultCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // 収入
            ['name' => '給与',       'type' => 'income',  'color_code' => '#059669'],
            ['name' => '副業',       'type' => 'income',  'color_code' => '#34d399'],
            ['name' => 'その他収入',  'type' => 'income',  'color_code' => '#a7f3d0'],
            // 支出
            ['name' => '食費',       'type' => 'expense', 'color_code' => '#f97316'],
            ['name' => '固定費',     'type' => 'expense', 'color_code' => '#1e40af'],
            ['name' => '日用品',     'type' => 'expense', 'color_code' => '#fbbf24'],
            ['name' => '交際費',     'type' => 'expense', 'color_code' => '#f472b6'],
            ['name' => '交通費',     'type' => 'expense', 'color_code' => '#0ea5e9'],
            ['name' => '医療費',     'type' => 'expense', 'color_code' => '#f43f5e'],
            ['name' => 'その他',     'type' => 'expense', 'color_code' => '#64748b'],
        ];

        // ユーザーを取得
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            foreach ($categories as $category) {
                Category::firstOrCreate(
                    // 検索条件（時重複をチェック）
                    [
                        'user_id' => $user->id,
                        'name' => $category['name'],
                        'type' => $category['type'],
                    ],
                    // カテゴリーが存在しない場合は作成
                    [
                        'color_code' => $category['color_code']
                ]
                );
            }
        }
    }
}
