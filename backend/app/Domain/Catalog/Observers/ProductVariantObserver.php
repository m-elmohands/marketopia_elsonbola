<?php

namespace App\Domain\Catalog\Observers;

use App\Domain\Catalog\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

class ProductVariantObserver
{
    public function saving(ProductVariant $record): void
    {
        if ((float) $record->weight_kg <= 0) {
            throw ValidationException::withMessages(['weight_kg' => __('Weight must be greater than zero.')]);
        }
        if ($record->exists && $record->isDirty('product_id')) {
            throw ValidationException::withMessages(['product_id' => __('A pack cannot be moved to another product.')]);
        }
        if (! $record->is_active) {
            $this->ensureAnotherPack($record);
        }
    }

    public function deleting(ProductVariant $record): void
    {
        if (! $record->isForceDeleting()) {
            $this->ensureAnotherPack($record);
        }
    }

    private function ensureAnotherPack(ProductVariant $record): void
    {
        $product = $record->product()->first();
        if ($product?->is_published && ! $product->variants()->where('is_active', true)->whereKeyNot($record->id)->exists()) {
            throw ValidationException::withMessages(['is_active' => __('Unpublish the product before removing its last active pack.')]);
        }
    }
}
