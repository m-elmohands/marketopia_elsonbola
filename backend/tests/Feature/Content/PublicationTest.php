<?php

namespace Tests\Feature\Content;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Content\Actions\ForceDeleteContent;
use App\Domain\Content\Actions\SetPublication;
use App\Domain\Content\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PublicationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_product_cannot_publish_without_an_active_pack(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = Product::factory()->create();
        $this->expectException(ValidationException::class);
        app(SetPublication::class)->execute($product, true);
    }

    public function test_product_cannot_publish_without_both_translations(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $product->id]);
        $product->translate('en')->delete();
        $product->refresh();
        $this->expectException(ValidationException::class);
        $product->update(['is_published' => true]);
    }

    public function test_last_active_pack_cannot_be_disabled_or_deleted(): void
    {
        $product = Product::factory()->create();
        $pack = ProductVariant::factory()->create(['product_id' => $product->id]);
        $product->update(['is_published' => true]);
        try {
            $pack->update(['is_active' => false]);
            $this->fail('Expected a validation failure');
        } catch (ValidationException) {
        }
        $pack->refresh();
        $this->expectException(ValidationException::class);
        $pack->delete();
    }

    public function test_published_translation_cannot_be_emptied(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $product->id]);
        $product->update(['is_published' => true]);
        $this->expectException(ValidationException::class);
        $product->translate('en')->update(['description' => '']);
    }

    public function test_page_publishing_restoring_and_locale_content(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $page = Page::factory()->create();
        $this->getJson('/api/v1/pages/about?locale=en')->assertNotFound();
        app(SetPublication::class)->execute($page, true);
        $this->getJson('/api/v1/pages/about?locale=en')->assertJsonPath('data.title', 'About');
        $page->delete();
        $this->getJson('/api/v1/pages/about')->assertNotFound();
        $page->restore();
        $this->getJson('/api/v1/pages/about')->assertOk();
        $this->assertFalse(Gate::allows('forceDelete', $page));
    }

    public function test_explicit_force_deletion_removes_variants_and_translations(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = Product::factory()->create();
        $pack = ProductVariant::factory()->create(['product_id' => $product->id]);
        $product->delete();
        app(ForceDeleteContent::class)->execute($product);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_variants', ['id' => $pack->id]);
        $this->assertDatabaseMissing('product_translations', ['product_id' => $product->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'force_deleted', 'subject_id' => $product->id, 'subject_type' => Product::class]);
    }

    public function test_an_editor_can_publish_but_cannot_delete(): void
    {
        $user = User::factory()->create(['role' => 'editor']);
        $this->actingAs($user);
        $product = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $product->id]);
        $this->assertTrue(Gate::allows('update', $product));
        $this->assertFalse(Gate::allows('delete', $product));
        app(SetPublication::class)->execute($product, true);
        $this->assertTrue($product->refresh()->is_published);
    }
}
