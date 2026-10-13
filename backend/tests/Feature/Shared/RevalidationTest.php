<?php

namespace Tests\Feature\Shared;

use App\Domain\Shared\Jobs\RevalidateFrontend;
use App\Domain\Shared\Services\ContentCache;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RevalidationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_invalidation_dispatches_only_after_commit_and_never_on_rollback(): void
    {
        config(['website.revalidation_url' => 'https://frontend.example/revalidate', 'website.revalidation_token' => 'test-token']);
        Queue::fake();
        DB::beginTransaction();
        app(ContentCache::class)->invalidate();
        Queue::assertNothingPushed();
        DB::rollBack();
        Queue::assertNothingPushed();
        DB::transaction(fn () => app(ContentCache::class)->invalidate());
        Queue::assertPushed(RevalidateFrontend::class);
    }

    public function test_revalidation_sends_configured_bearer_token_and_tag(): void
    {
        config(['website.revalidation_url' => 'https://frontend.example/revalidate', 'website.revalidation_token' => 'test-token']);
        Http::fake(['*' => Http::response([], 200)]);
        (new RevalidateFrontend)->handle();
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token') && $request['tags'] === ['website-content']);
    }
}
