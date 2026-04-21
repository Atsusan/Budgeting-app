<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    // ユーザーがこのリクエストを行うことができるかどうかを判断
    public function authorize(): bool
    {
        return true;
    }

    // リクエストのバリデーションルールを定義
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'color_code' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
        ];
    }
}
