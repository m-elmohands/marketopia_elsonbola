<?php

namespace Tests\Feature\Catalog;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicProductsTest extends TestCase
{
    use DatabaseMigrations;

    private function published(array $attributes = []): Product
    {
        $product = Product::factory()->create($attributes);
        ProductVariant::factory()->create(['product_id' => $product->id]);
        $product->update(['is_published' => true]);

        return $product;
    }

    public function test_only_published_products_and_active_packs_are_public(): void
    {
        $product = $this->published();
        Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $product->id, 'weight_kg' => 100, 'is_active' => false]);
        $this->getJson('/api/v1/products')->assertOk()->assertJsonCount(1, 'data')->assertJsonCount(1, 'data.0.variants')->assertJsonPath('data.0.name', 'أرز مصري')->assertJsonPath('meta.total', 1);
        $this->getJson('/api/v1/products/'.$product->slug.'?locale=en')->assertOk()->assertJsonPath('data.name', 'Egyptian rice')->assertJsonMissingPath('data.deleted_at');
        $product->delete();
        $this->getJson('/api/v1/products/'.$product->slug)->assertNotFound()->assertJsonPath('message', 'Content not found');
    }

    public function test_filters_sorting_search_and_pagination(): void
    {
        $a = $this->published(['is_featured' => true, 'en' => ['name' => 'Alpha', 'short_description' => 'Long grain', 'description' => 'Details']]);
        $b = $this->published(['en' => ['name' => 'Beta', 'short_description' => 'Rice', 'description' => 'Details']]);
        $this->getJson('/api/v1/products?locale=en&featured=1')->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $a->id);
        $this->getJson('/api/v1/products?locale=en&search=Long')->assertJsonPath('meta.total', 1);
        $this->getJson('/api/v1/products?locale=en&sort=-name&per_page=1')->assertJsonPath('data.0.id', $b->id)->assertJsonPath('meta.last_page', 2);
        $this->getJson('/api/v1/products?locale=en&sort=-name&per_page=1&page=2')->assertJsonPath('data.0.id', $a->id);
    }

    public function test_validation_and_method_errors_use_the_api_envelope(): void
    {
        foreach (['locale=fr', 'per_page=51', 'page=0', 'sort=password', 'featured=yes'] as $query) {
            $this->getJson('/api/v1/products?'.$query)->assertUnprocessable()->assertJsonStructure(['data', 'message', 'errors', 'meta']);
        }
        $this->postJson('/api/v1/products', [])->assertStatus(405)->assertJsonPath('message', 'Method not allowed');
        $this->getJson('/api/v1/products/missing')->assertNotFound();
    }

    public function test_translations_and_content_changes_invalidate_cached_responses(): void
    {
        $product = $this->published();
        $this->getJson('/api/v1/products/'.$product->slug.'?locale=en')->assertJsonPath('data.name', 'Egyptian rice');
        $this->getJson('/api/v1/products/'.$product->slug.'?locale=ar')->assertJsonPath('data.name', 'أرز مصري');
        $product->translate('en')->update(['name' => 'Updated rice']);
        $this->getJson('/api/v1/products/'.$product->slug.'?locale=en')->assertJsonPath('data.name', 'Updated rice');
        $product->update(['is_published' => false]);
        $this->getJson('/api/v1/products/'.$product->slug.'?locale=en')->assertNotFound();
    }

    public function test_media_has_localized_alternatives_and_safe_conversion_fallbacks(): void
    {
        Storage::fake('public');
        Queue::fake();
        $product = $this->published();
        $media = $product->addMedia(UploadedFile::fake()->image('rice.jpg'))->withCustomProperties(['alt' => ['ar' => 'أرز', 'en' => 'Rice']])->toMediaCollection('cover');
        $this->getJson('/api/v1/products/'.$product->slug.'?locale=en')->assertJsonPath('data.media.0.alt', 'Rice')->assertJsonPath('data.media.0.web_url', $media->getUrl());
        $media->setCustomProperty('alt.en', 'Rice pack')->save();
        $this->getJson('/api/v1/products/'.$product->slug.'?locale=en')->assertJsonPath('data.media.0.alt', 'Rice pack');
        $media->delete();
        $this->getJson('/api/v1/products/'.$product->slug)->assertJsonCount(0, 'data.media');
    }

    public function test_catalog_query_count_does_not_grow_with_product_count(): void
    {
        $this->published();
        DB::enableQueryLog();
        $this->getJson('/api/v1/products?locale=en')->assertOk();
        $first = count(DB::getQueryLog());
        DB::disableQueryLog();
        for ($index = 0; $index < 9; $index++) {
            $this->published();
        }
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->getJson('/api/v1/products?locale=en')->assertJsonCount(10, 'data');
        $this->assertLessThanOrEqual($first + 1, count(DB::getQueryLog()));
        DB::disableQueryLog();
    }

    public function test_details_query_uses_ids_and_respects_publication_and_cache_invalidation(): void
    {
        $product = $this->published();
        foreach (['ar' => 'أرز مصري', 'en' => 'Egyptian rice'] as $locale => $name) {
            $this->getJson('/api/v1/products/details?id='.$product->id.'&locale='.$locale)
                ->assertOk()->assertJsonPath('data.id', $product->id)->assertJsonPath('data.name', $name)->assertJsonCount(1, 'data.variants');
        }
        $product->translate('en')->update(['name' => 'Updated rice']);
        $this->getJson('/api/v1/products/details?id='.$product->id.'&locale=en')->assertJsonPath('data.name', 'Updated rice');
        $product->update(['is_published' => false]);
        $this->getJson('/api/v1/products/details?id='.$product->id)->assertNotFound();
        $product->update(['is_published' => true]);
        $product->delete();
        $this->getJson('/api/v1/products/details?id='.$product->id)->assertNotFound();
        $this->getJson('/api/v1/products/details?id=999999')->assertNotFound();
    }

    public function test_details_query_validates_ids_and_locale(): void
    {
        foreach (['', 'id=0', 'id=-1', 'id=1.5', 'id=abc', 'id=01', 'id[]=1', 'id=9007199254740992', 'id=1&locale=fr'] as $query) {
            $this->getJson('/api/v1/products/details?'.$query)->assertUnprocessable()->assertJsonStructure(['data', 'message', 'errors', 'meta']);
        }
    }
}
