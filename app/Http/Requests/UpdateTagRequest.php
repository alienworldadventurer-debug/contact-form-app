<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                // 自身のIDを重複チェックから除外する
                Rule::unique('tags', 'name')->ignore($this->route('tag')),
            ],
        ];
    }

    /**
     * カスタムエラーメッセージ
     */
    public function messages(): array
    {
        return [
            'name.required' => 'タグ名を入力してください',
            'name.max' => 'タグ名は50文字以内で入力してください',
            'name.unique' => 'そのタグ名は使用されています',
        ];
    }
}
