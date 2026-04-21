<?php

namespace App\Actions;

use App\Models\Category;
use App\Models\User;

class CreateDefaultCategories
{
    public function execute(User $user): void
    {
        foreach (config('transaction.default_categories') as $cat) {
            Category::create([
                'user_id' => $user->id,
                'name' => $cat['name'],
                'type' => $cat['type'],
                'color_code' => $cat['color_code'],
            ]);
        }
    }
}
