<?php

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class BrowseProducts
{
    public function query(string $locale): Builder
    {
        return Product::query()->published()->whereHas('translations', fn (Builder $q) => $q->where('locale', $locale))
            ->with(['translations', 'media', 'variants' => fn ($q) => $q->where('is_active', true)->with('media')]);
    }

    public function findById(int $id, string $locale): Product
    {
        return $this->query($locale)->whereKey($id)->firstOrFail();
    }

    public function execute(array $filters, string $locale): LengthAwarePaginator
    {
        $query = $this->query($locale);
        if (isset($filters['featured'])) {
            $query->where('is_featured', (bool) $filters['featured']);
        }
        if (filled($filters['search'] ?? null)) {
            $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filters['search']);
            $query->whereHas('translations', fn (Builder $q) => $q->where('locale', $locale)->where(fn (Builder $q) => $q->where('name', 'like', "%$search%")->orWhere('short_description', 'like', "%$search%")));
        }
        $sort = $filters['sort'] ?? 'sort_order';
        if (in_array($sort, ['name', '-name'], true)) {
            $query->orderBy(ProductTranslation::select('name')->whereColumn('product_id', 'products.id')->where('locale', $locale)->limit(1), $sort === 'name' ? 'asc' : 'desc');
        } else {
            $query->orderBy($sort === 'newest' ? 'created_at' : 'sort_order', $sort === 'newest' ? 'desc' : 'asc');
        }

        return $query->orderBy('id')->paginate($filters['per_page'] ?? 12, ['*'], 'page', $filters['page'] ?? 1);
    }
}
