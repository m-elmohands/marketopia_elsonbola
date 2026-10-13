<?php

namespace App\Domain\Administration\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetAdminLocaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_active && ! $this->user()->trashed();
    }

    public function rules(): array
    {
        return ['locale' => ['required', Rule::in(['ar', 'en'])]];
    }
}
