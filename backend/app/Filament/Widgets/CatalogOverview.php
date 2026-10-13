<?php

namespace App\Filament\Widgets;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CatalogOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [Stat::make(__('Published products'), Product::published()->count()), Stat::make(__('Draft products'), Product::where('is_published', false)->count()), Stat::make(__('Active pack sizes'), ProductVariant::where('is_active', true)->whereHas('product')->count())];
    }
}
