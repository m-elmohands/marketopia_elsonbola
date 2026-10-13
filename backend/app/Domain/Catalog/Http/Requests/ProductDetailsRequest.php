<?php

namespace App\Domain\Catalog\Http\Requests;

use App\Domain\Shared\Http\Requests\PublicContentRequest;

class ProductDetailsRequest extends PublicContentRequest
{
    public function rules(): array
    {
        return [
            'locale' => parent::rules()['locale'],
            'id' => ['required', 'integer', 'min:1', 'max:9007199254740991', 'regex:/^[1-9][0-9]*$/'],
        ];
    }
}
