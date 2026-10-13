<?php

namespace Tests\Unit\Content;

use App\Domain\Content\Models\Page;
use App\Domain\Content\Services\PublicationValidator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PublicationValidatorTest extends TestCase
{
    public function test_home_requires_translated_hero_copy(): void
    {
        $page = new Page(['key' => 'home', 'ar' => ['title' => 'الرئيسية', 'intro' => 'أرز مصري'], 'en' => ['title' => 'Home', 'intro' => 'Egyptian rice']]);
        app(PublicationValidator::class)->validate($page);
        $this->assertSame('Home', $page->translate('en')->title);
    }

    public function test_about_requires_the_business_story_in_both_languages(): void
    {
        $page = new Page(['key' => 'about', 'ar' => ['title' => 'من نحن', 'intro' => 'نبذة'], 'en' => ['title' => 'About', 'intro' => 'Introduction']]);
        $this->expectException(ValidationException::class);
        app(PublicationValidator::class)->validate($page);
    }
}
