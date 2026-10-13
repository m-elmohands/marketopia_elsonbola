<?php

namespace App\Domain\Settings\Actions;

use App\Domain\Settings\Models\BusinessSetting;
use App\Domain\Settings\Models\ContactNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SaveContactNumber
{
    public function execute(array $data, ?ContactNumber $record = null): ContactNumber
    {
        Gate::authorize($record ? 'update' : 'create', $record ?? ContactNumber::class);
        Validator::make($data, ['phone' => ['required', 'regex:/^\+[1-9]\d{7,14}$/'], 'is_primary' => ['boolean'], 'sort_order' => ['integer', 'min:0']])->validate();

        return DB::transaction(function () use ($data, $record) {
            BusinessSetting::query()->whereKey(1)->lockForUpdate()->firstOrFail();
            $data['business_setting_id'] = 1;
            $others = ContactNumber::query()->when($record, fn ($q) => $q->whereKeyNot($record->id));
            if (! $others->exists()) {
                $data['is_primary'] = true;
            }
            if ($data['is_primary'] ?? false) {
                foreach ((clone $others)->where('is_primary', true)->get() as $other) {
                    $other->update(['is_primary' => false]);
                }
            } elseif ($record?->is_primary) {
                throw ValidationException::withMessages(['is_primary' => __('Choose another primary number first.')]);
            }
            $record ??= new ContactNumber;
            $record->fill($data)->save();

            return $record;
        });
    }
}
