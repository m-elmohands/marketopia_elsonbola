<?php

namespace App\Domain\Shared\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locale' => ['sometimes', Rule::in(['ar', 'en'])],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'featured' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', Rule::in(['sort_order', 'name', '-name', 'newest'])],
        ];
    }

    public function locale(): string
    {
        return $this->validated('locale', 'ar');
    }
}
