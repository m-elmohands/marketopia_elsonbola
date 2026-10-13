<?php

namespace Database\Factories\Content;

use App\Domain\Content\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'key' => 'about',
            'is_published' => false,
            'ar' => ['title' => 'من نحن', 'intro' => 'نبذة عن الشركة', 'body' => 'قصة الشركة'],
            'en' => ['title' => 'About', 'intro' => 'About our business', 'body' => 'Our story'],
        ];
    }
}
