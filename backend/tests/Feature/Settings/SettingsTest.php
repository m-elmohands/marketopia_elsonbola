<?php

namespace Tests\Feature\Settings;

use App\Domain\Content\Models\Page;
use App\Domain\Settings\Actions\SaveContactNumber;
use App\Domain\Settings\Models\BusinessSetting;
use App\Domain\Settings\Models\ContactNumber;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_settings_are_localized_and_contacts_are_sorted(): void
    {
        BusinessSetting::factory()->create(['email' => 'info@example.com']);
        ContactNumber::factory()->create(['business_setting_id' => 1]);
        $this->getJson('/api/v1/settings?locale=en')->assertOk()->assertJsonPath('data.brand_name', 'Golden Spike')->assertJsonPath('data.contact_numbers.0.phone', '+201005263014')->assertJsonMissingPath('data.id');
        $this->getJson('/api/v1/settings?locale=ar')->assertJsonPath('data.brand_name', 'السنبلة الذهبية');
    }

    public function test_primary_number_switches_atomically(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        BusinessSetting::factory()->create();
        $first = app(SaveContactNumber::class)->execute(['phone' => '+201005263014', 'is_primary' => false]);
        $this->assertTrue($first->is_primary);
        $second = app(SaveContactNumber::class)->execute(['phone' => '+201029035139', 'is_primary' => true]);
        $this->assertFalse($first->refresh()->is_primary);
        $this->assertTrue($second->is_primary);
        $this->expectException(ValidationException::class);
        $second->delete();
    }

    public function test_invalid_phone_is_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        BusinessSetting::factory()->create();
        $this->expectException(ValidationException::class);
        app(SaveContactNumber::class)->execute(['phone' => '123']);
    }

    public function test_seed_is_idempotent_and_preserves_deleted_pages(): void
    {
        Storage::fake('public');
        Queue::fake();
        $this->seed();
        $this->seed();
        $this->assertDatabaseCount('business_settings', 1);
        $this->assertDatabaseCount('pages', 3);
        $this->assertDatabaseCount('products', 1);
        Page::where('key', 'home')->firstOrFail()->delete();
        $this->seed();
        $this->assertDatabaseCount('pages', 3);
        $this->getJson('/api/v1/pages/home')->assertNotFound();
    }
}
