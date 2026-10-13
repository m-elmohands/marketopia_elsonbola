<?php

namespace App\Domain\Administration\Http\Controllers;

use App\Domain\Administration\Actions\SetAdminLocale;
use App\Domain\Administration\Http\Requests\SetAdminLocaleRequest;
use Illuminate\Http\RedirectResponse;

class AdminLocaleController
{
    public function __invoke(SetAdminLocaleRequest $request, SetAdminLocale $action): RedirectResponse
    {
        $action->execute($request->validated('locale'));

        return redirect('/admin');
    }
}
