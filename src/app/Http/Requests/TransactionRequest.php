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

    public function messages(): array
    {
        $messages = [];
        $rows =$this->input('rows', []);

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 1;
            $messages["rows.{$index}.amount.required"] = "{$lineNumber}行目の金額は必須です。";
            $messages["rows.{$index}.amount.integer"] = "{$lineNumber}行目の金額は整数で入力してください。";
            $messages["rows.{$index}.amount.min"] = "{$lineNumber}行目の金額は1以上で入力してください。";
            $messages["rows.{$index}.date.required"] = "{$lineNumber}行目の日付は必須です。";
            $messages["rows.{$index}.date.date"] = "{$lineNumber}行目の日付は日付形式で入力してください。";
            $messages["rows.{$index}.category_id.required"] = "{$lineNumber}行目のカテゴリーは必須です。";
            $messages["rows.{$index}.category_id.integer"] = "{$lineNumber}行目のカテゴリーは整数で入力してください。";
            $messages["rows.{$index}.category_id.exists"] = "{$lineNumber}行目のカテゴリーは存在しません。";
            $messages["rows.{$index}.description.string"] = "{$lineNumber}行目の内容は文字列で入力してください。";
            $messages["rows.{$index}.description.max"] = "{$lineNumber}行目の内容は255文字以内で入力してください。";
        }
        return $messages;
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
