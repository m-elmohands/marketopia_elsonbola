<?php

namespace App\Domain\Administration\Actions;

class SetAdminLocale
{
    public function execute(string $locale): void
    {
        session(['admin_locale' => $locale]);
    }
}
