<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/../vendor/autoload.php';

// This process always uses an isolated, disposable database and fake media storage.
foreach (['APP_ENV' => 'testing', 'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'sync', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:', 'DB_URL' => ''] as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $_SERVER[$key] = $value;
}
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(ConsoleKernel::class)->bootstrap();
set_exception_handler(static function (Throwable $exception): never {
    fwrite(STDERR, 'Fixture export failed: '.$exception->getMessage()."\n");
    exit(1);
});
if (! $app->environment('testing')) {
    throw new RuntimeException('Clear cached configuration before exporting test fixtures.');
}
config([
    'database.default' => 'sqlite',
    'database.connections.sqlite.database' => ':memory:',
    'database.connections.sqlite.url' => null,
    'cache.default' => 'array',
    'session.driver' => 'array',
    'app.key' => 'base64:'.base64_encode(random_bytes(32)),
    'filesystems.disks.public.url' => 'http://localhost:8000/storage',
    'website.revalidation_url' => null,
    'website.revalidation_token' => null,
]);
DB::purge('sqlite');
if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
    throw new RuntimeException('Fixture export requires an in-memory database.');
}
Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
Queue::fake();
Artisan::call('migrate', ['--force' => true]);
$app->make(DatabaseSeeder::class)->run();

$kernel = $app->make(HttpKernel::class);
$fixtures = [];
foreach (['ar', 'en'] as $locale) {
    $requests = [
        '/api/v1/settings?locale='.$locale,
        '/api/v1/pages/home?locale='.$locale,
        '/api/v1/pages/about?locale='.$locale,
        '/api/v1/pages/contact?locale='.$locale,
        '/api/v1/products?locale='.$locale.'&featured=1&per_page=3',
        '/api/v1/products?locale='.$locale.'&sort=sort_order&page=1&per_page=12',
        '/api/v1/products?locale='.$locale.'&per_page=50',
        '/api/v1/products/details?locale='.$locale.'&id=1',
    ];
    foreach ($requests as $path) {
        $request = Request::create($path, 'GET', server: ['HTTP_ACCEPT' => 'application/json']);
        $response = $kernel->handle($request);
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Fixture request failed: '.$path.' ('.$response->getStatusCode().'): '.($response->exception?->getMessage() ?? $response->getContent()));
        }
        $fixtures[$path] = json_decode($response->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $kernel->terminate($request, $response);
    }
}

$directory = __DIR__.'/../../frontend/src/features/website/tests/fixtures';
if (! is_dir($directory) && ! mkdir($directory, 0775, true)) {
    throw new RuntimeException('Cannot create frontend fixture directory.');
}
$file = $directory.'/laravel-content.json';
$json = json_encode($fixtures, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
$json = preg_replace_callback('/^ +/m', fn (array $match) => str_repeat(' ', intdiv(strlen($match[0]), 2)), $json);
if (file_put_contents($file, $json."\n") === false) {
    throw new RuntimeException('Cannot write frontend content fixtures.');
}
echo 'Exported '.count($fixtures)." real Laravel API responses from an isolated SQLite database.\n";
