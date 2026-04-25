<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    // ユーザーがこのリクエストを行うことができるかどうかを判断
    public function authorize(): bool
    {
        return auth()->check();
    }

    // リクエストのバリデーションルールを定義
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }
}
