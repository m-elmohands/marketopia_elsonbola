<?php

namespace App\Domain\Shared\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class RevalidateFrontend implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $uniqueFor = 60;

    public function uniqueId(): string
    {
        return 'website-content';
    }

    public function backoff(): array
    {
        return [10, 30, 120];
    }

    public function handle(): void
    {
        Http::timeout(10)->withToken(config('website.revalidation_token'))->post(config('website.revalidation_url'), ['tags' => ['website-content']])->throw();
    }
}
