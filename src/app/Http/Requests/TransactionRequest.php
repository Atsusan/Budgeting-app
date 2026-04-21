<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    // ユーザーがこのリクエストを行うことができるかどうかを判断
    public function authorize(): bool
    {
        return auth()->check();
    }

    // バリデーション前に実行
    public function prepareForValidation(): void
    {
        $this->merge([
            'rows' => json_decode($this->input('rows'), true),
        ]);
    }

    // リクエストのバリデーションルールを定義
    public function rules(): array
    {
        return [
            'rows'               => ['required', 'array'],
            'rows.*.date'        => ['required', 'date'],
            'rows.*.category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('user_id', auth()->id()),
            ],
            'rows.*.amount'      => ['required', 'integer', 'min:1'],
            'rows.*.description' => ['nullable', 'string'],
        ];
    }
}
