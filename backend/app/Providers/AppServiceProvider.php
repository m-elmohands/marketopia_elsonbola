<?php

namespace App\Providers;

use App\Domain\Administration\Observers\UserObserver;
use App\Domain\Administration\Policies\UserPolicy;
use App\Domain\Audit\Models\AuditLog;
use App\Domain\Audit\Policies\AuditLogPolicy;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductTranslation;
use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Catalog\Observers\ProductVariantObserver;
use App\Domain\Content\Models\Page;
use App\Domain\Content\Models\PageTranslation;
use App\Domain\Content\Policies\PagePolicy;
use App\Domain\Settings\Models\BusinessSetting;
use App\Domain\Settings\Models\BusinessSettingTranslation;
use App\Domain\Settings\Models\ContactNumber;
use App\Domain\Settings\Observers\SettingsObserver;
use App\Domain\Settings\Policies\BusinessSettingPolicy;
use App\Domain\Shared\Observers\TranslationObserver;
use App\Domain\Shared\Observers\WebsiteContentObserver;
use App\Domain\Shared\Policies\ContentPolicy;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        foreach ([Product::class, ProductVariant::class, ContactNumber::class] as $model) {
            Gate::policy($model, ContentPolicy::class);
        }
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(BusinessSetting::class, BusinessSettingPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        foreach ([Product::class, ProductVariant::class, Page::class, BusinessSetting::class, ContactNumber::class, User::class, Media::class] as $model) {
            $model::observe(WebsiteContentObserver::class);
        }
        foreach ([ProductTranslation::class, PageTranslation::class, BusinessSettingTranslation::class] as $model) {
            $model::observe(TranslationObserver::class);
        }
        ProductVariant::observe(ProductVariantObserver::class);
        BusinessSetting::observe(SettingsObserver::class);
        ContactNumber::observe(SettingsObserver::class);
        User::observe(UserObserver::class);
        RateLimiter::for('public-content', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
    }
}
