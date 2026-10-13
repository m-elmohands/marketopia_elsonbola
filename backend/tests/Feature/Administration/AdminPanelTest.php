<?php

namespace Tests\Feature\Administration;

use App\Domain\Audit\Models\AuditLog;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Settings\Models\BusinessSetting;
use App\Filament\Resources\ContactNumbers\Pages\CreateContactNumber;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\ProductVariants\Pages\CreateProductVariant;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Queue::fake();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_guests_are_redirected_and_inactive_accounts_are_denied(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $user = User::factory()->create(['is_active' => false]);
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_pages_render_in_both_languages(): void
    {
        $this->seed();
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $this->actingAs($user);
        foreach (['ar', 'en'] as $locale) {
            $this->withSession(['admin_locale' => $locale]);
            foreach (['', '/products', '/products/create', '/products/'.$product->id.'/edit', '/product-variants/create', '/pages', '/business-settings/1/edit', '/contact-numbers/create', '/users/create', '/audit-logs'] as $path) {
                $this->get('/admin'.$path)->assertOk()->assertSee('dir="'.($locale === 'ar' ? 'rtl' : 'ltr').'"', false);
            }
        }
    }

    public function test_editor_cannot_access_user_management_or_settings_editing(): void
    {
        $this->seed();
        $this->actingAs(User::factory()->create(['role' => 'editor']));
        $this->get('/admin/products')->assertOk();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/business-settings/1/edit')->assertForbidden();
        $this->get('/admin/audit-logs')->assertForbidden();
    }

    public function test_product_creation_and_editing_persist_both_translations(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        Livewire::test(CreateProduct::class)->fillForm(['slug' => 'premium-rice', 'is_featured' => true, 'sort_order' => 1, 'ar' => ['name' => 'أرز ممتاز', 'short_description' => 'وصف', 'description' => 'تفاصيل'], 'en' => ['name' => 'Premium rice', 'short_description' => 'Summary', 'description' => 'Details']])->call('create')->assertHasNoFormErrors();
        $product = Product::where('slug', 'premium-rice')->firstOrFail();
        $this->assertFalse($product->is_published);
        $this->assertSame('Premium rice', $product->translate('en')->name);
        Livewire::test(EditProduct::class, ['record' => $product->id])->assertFormSet(['en.name' => 'Premium rice', 'ar.name' => 'أرز ممتاز'])->fillForm(['en.name' => 'Updated rice'])->call('save')->assertHasNoFormErrors();
        $this->assertSame('Updated rice', $product->refresh()->translate('en')->name);
        Livewire::test(ListProducts::class)->assertCanSeeTableRecords([$product]);
    }

    public function test_pack_form_creates_a_variant_and_checks_duplicate_weights(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = Product::factory()->create();
        $data = ['product_id' => $product->id, 'weight_kg' => 5, 'is_active' => true, 'sort_order' => 0];
        Livewire::test(CreateProductVariant::class)->fillForm($data)->call('create')->assertHasNoFormErrors();
        Livewire::test(CreateProductVariant::class)->fillForm($data)->call('create')->assertHasFormErrors(['weight_kg' => 'unique']);
    }

    public function test_contact_form_uses_the_primary_number_service(): void
    {
        BusinessSetting::factory()->create();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        Livewire::test(CreateContactNumber::class)->fillForm(['phone' => '+201005263014', 'is_primary' => false, 'sort_order' => 0])->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('contact_numbers', ['phone' => '+201005263014', 'is_primary' => true]);
    }

    public function test_admin_language_change_is_validated(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post('/admin/locale', ['locale' => 'en'])->assertRedirect('/admin')->assertSessionHas('admin_locale', 'en');
        $this->post('/admin/locale', ['locale' => 'fr'])->assertSessionHasErrors('locale');
    }

    public function test_profile_menu_switches_between_languages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        foreach (['ar' => 'en', 'en' => 'ar'] as $currentLocale => $targetLocale) {
            $this->withSession(['admin_locale' => $currentLocale])
                ->get('/admin')
                ->assertOk()
                ->assertDontSee('id="admin-locale"', false);

            $action = Filament::getPanel('admin')->getUserMenuItems()['switchLanguage'];
            $this->assertSame($targetLocale === 'en' ? 'English' : 'العربية', $action->getLabel());
            $this->assertTrue($action->shouldPostToUrl());
            $this->assertSame(route('admin.locale', ['locale' => $targetLocale]), $action->getUrl());

            $this->post($action->getUrl())
                ->assertRedirect('/admin')
                ->assertSessionHas('admin_locale', $targetLocale);
            $this->get('/admin')->assertOk()
                ->assertSee('dir="'.($targetLocale === 'ar' ? 'rtl' : 'ltr').'"', false);
        }
    }

    public function test_last_administrator_cannot_be_deactivated(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->expectException(ValidationException::class);
        $user->update(['is_active' => false]);
    }

    public function test_audit_does_not_record_passwords(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);
        $user->update(['password' => 'different-secret-password']);
        $log = AuditLog::where('action', 'updated')->where('subject_type', User::class)->latest('id')->firstOrFail();
        $this->assertTrue($log->changes['credential_changed']);
        $this->assertArrayNotHasKey('password', $log->changes['attributes']);
        $this->assertSame($user->id, $log->administrator_id);
    }

    public function test_publication_action_and_recovery_work_in_livewire(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $product->id]);
        Livewire::test(EditProduct::class, ['record' => $product->id])->callAction('publication');
        $this->assertTrue($product->refresh()->is_published);
        Livewire::test(EditProduct::class, ['record' => $product->id])->callAction('delete');
        $this->assertTrue($product->refresh()->trashed());
        Livewire::test(EditProduct::class, ['record' => $product->id])->callAction('restore');
        $this->assertFalse($product->refresh()->trashed());
    }

    public function test_a_failed_publication_notifies_and_preserves_draft_state(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = Product::factory()->create();
        Livewire::test(EditProduct::class, ['record' => $product->id])->callAction('publication')->assertNotified();
        $this->assertFalse($product->refresh()->is_published);
    }

    public function test_editors_cannot_delete_through_a_livewire_action(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'editor']));
        $product = Product::factory()->create();
        Livewire::test(EditProduct::class, ['record' => $product->id])->assertActionHidden('delete');
        $this->assertFalse($product->refresh()->trashed());
    }

    public function test_media_upload_persists_localized_descriptions(): void
    {
        Storage::fake('public');
        Queue::fake();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $file = UploadedFile::fake()->image('rice.jpg');
        Livewire::test(CreateProduct::class)
            ->fillForm(['slug' => 'rice-with-image', 'sort_order' => 0,
                'ar' => ['name' => 'أرز مصري'], 'en' => ['name' => 'Egyptian rice'], 'cover' => [$file]])
            ->call('create')->assertHasNoFormErrors();
        $product = Product::where('slug', 'rice-with-image')->firstOrFail();
        $this->assertSame('Egyptian rice', $product->getFirstMedia('cover')->getCustomProperty('alt.en'));
        $this->assertSame('أرز مصري', $product->getFirstMedia('cover')->getCustomProperty('alt.ar'));
    }

    public function test_user_form_hashes_passwords_and_validates_roles(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Content editor', 'email' => 'editor@example.com',
                'password' => 'strong-example-password', 'role' => 'editor', 'is_active' => true])
            ->call('create')->assertHasNoFormErrors();
        $editor = User::where('email', 'editor@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('strong-example-password', $editor->password));
        $this->assertSame('editor', $editor->role);
        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Invalid user', 'email' => 'invalid@example.com',
                'password' => 'short', 'role' => 'owner', 'is_active' => true])
            ->call('create')->assertHasFormErrors(['password', 'role']);
    }
}
