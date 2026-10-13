<?php

namespace Database\Seeders;

use App\Domain\Content\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    use SeedTranslations;

    public function run(): void
    {
        foreach (require __DIR__.'/data/pages.php' as $key => $translations) {
            $page = Page::withTrashed()->firstOrNew(['key' => $key]);
            if ($page->trashed()) {
                continue;
            }
            $isNew = ! $page->exists;
            // Upgrade only the untouched title-only records from the original seeder.
            $placeholder = $page->exists && ! $page->is_published && $page->translations->count() === 2;
            $originalTitles = ['home' => ['ar' => 'الرئيسية', 'en' => 'Home'], 'about' => ['ar' => 'من نحن', 'en' => 'About'], 'contact' => ['ar' => 'تواصل معنا', 'en' => 'Contact']];
            foreach (['ar', 'en'] as $locale) {
                $translation = $page->translate($locale, false);
                $placeholder = $placeholder && $translation && $translation->title === $originalTitles[$key][$locale]
                    && blank($translation->intro) && blank($translation->body) && blank($translation->meta_title) && blank($translation->meta_description);
            }
            if ($isNew) {
                $page->is_published = false;
            }
            if ($placeholder) {
                foreach (['ar', 'en'] as $locale) {
                    $page->translate($locale)->title = $translations[$locale]['title'];
                }
            }
            $this->fillTranslations($page, $translations);
            if ($isNew || $placeholder) {
                $page->update(['is_published' => true]);
            }
        }
    }
}
