<?php

namespace Tests\Feature\Content;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Content\Models\Page;
use App\Domain\Settings\Models\BusinessSetting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class WebsiteSeederTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Queue::fake();
    }

    public function test_initial_content_is_complete_and_public_in_both_languages(): void
    {
        $this->getJson('/api/v1/products')->assertJsonCount(0, 'data');
        $this->seed();
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('product_variants', 4);
        $this->assertDatabaseCount('pages', 3);
        $this->assertDatabaseCount('users', 0);
        $this->assertSame(['1.000', '5.000', '10.000', '25.000'], Product::first()->variants->pluck('weight_kg')->all());
        foreach (['ar', 'en'] as $locale) {
            $this->getJson('/api/v1/products?locale='.$locale)->assertOk()->assertJsonCount(1, 'data')->assertJsonCount(4, 'data.0.variants');
            foreach (['home', 'about', 'contact'] as $key) {
                $this->getJson('/api/v1/pages/'.$key.'?locale='.$locale)->assertOk();
                $translation = Page::where('key', $key)->first()->translate($locale, false);
                foreach (['title', 'intro', 'body', 'meta_title', 'meta_description'] as $field) {
                    $this->assertNotEmpty($translation->{$field});
                    $this->assertStringNotContainsString('<p>', $translation->{$field});
                }
            }
        }
        $settings = BusinessSetting::findOrFail(1);
        $this->assertSame('+201005263014', $settings->contactNumbers->first()->phone);
        $this->assertNotNull($settings->getFirstMedia('logo'));
        $this->assertSame('Golden Spike logo', $settings->getFirstMedia('logo')->getCustomProperty('alt.en'));
        foreach (Media::all() as $media) {
            Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
            $this->assertNotEmpty($media->getCustomProperty('alt.ar'));
            $this->assertNotEmpty($media->getCustomProperty('alt.en'));
        }
        $this->assertFileExists(database_path('seeders/assets/logo.jpg'));
        $this->assertStringNotContainsString('<p>', Product::first()->translate('en')->description);
    }

    public function test_repeated_seeding_preserves_content_publication_and_media(): void
    {
        $this->seed();
        $product = Product::first();
        $product->is_published = false;
        $product->translate('en')->name = 'Administrator rice';
        $product->save();
        $variant = $product->variants()->first();
        $variant->update(['is_active' => false, 'sort_order' => 8]);
        $page = Page::where('key', 'about')->first();
        $page->is_published = false;
        $page->translate('ar')->body = '<p>محتوى مخصص</p>';
        $page->save();
        $settings = BusinessSetting::find(1);
        $settings->update(['email' => 'admin@example.test']);
        $mediaIds = Media::pluck('id')->all();
        $this->seed();
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('product_variants', 4);
        $this->assertDatabaseCount('page_translations', 6);
        $this->assertDatabaseCount('product_translations', 2);
        $this->assertDatabaseCount('contact_numbers', 1);
        $this->assertSame($mediaIds, Media::pluck('id')->all());
        $this->assertFalse($product->refresh()->is_published);
        $this->assertSame('Administrator rice', $product->translate('en')->name);
        $this->assertFalse($variant->refresh()->is_active);
        $this->assertSame(8, $variant->sort_order);
        $this->assertFalse($page->refresh()->is_published);
        $this->assertSame('<p>محتوى مخصص</p>', $page->translate('ar')->body);
        $this->assertSame('admin@example.test', $settings->refresh()->email);
    }

    public function test_deleted_content_and_packs_are_not_restored(): void
    {
        $this->seed();
        $variant = ProductVariant::first();
        $variant->delete();
        $page = Page::where('key', 'home')->first();
        $page->delete();
        $this->seed();
        $this->assertTrue($variant->refresh()->trashed());
        $this->assertTrue($page->refresh()->trashed());
        $product = Product::first();
        $product->delete();
        $this->seed();
        $this->assertTrue($product->refresh()->trashed());
        $this->assertDatabaseCount('products', 1);
    }

    public function test_original_placeholders_are_completed_and_published(): void
    {
        Page::create(['key' => 'home', 'ar' => ['title' => 'الرئيسية'], 'en' => ['title' => 'Home']]);
        $this->seed();
        $page = Page::where('key', 'home')->first();
        $this->assertTrue($page->is_published);
        $this->assertSame('Egyptian rice, at the heart of your table.', $page->translate('en')->title);
    }

    public function test_custom_drafts_and_missing_translations_are_preserved_and_completed(): void
    {
        $product = Product::factory()->create(['slug' => 'egyptian-rice']);
        $page = Page::factory()->create(['key' => 'contact', 'en' => ['title' => 'Our custom contact page']]);
        $page->translate('ar')->delete();
        $this->seed();
        $this->assertFalse($product->refresh()->is_published);
        $this->assertFalse($page->refresh()->is_published);
        $this->assertSame('Our custom contact page', $page->translate('en')->title);
        $this->assertNotEmpty($page->translate('ar', false)->body);
    }
}
