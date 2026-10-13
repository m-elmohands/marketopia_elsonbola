<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());
        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }
            $status = match (true) {
                $exception instanceof ValidationException => 422,$exception instanceof AuthorizationException => 403,$exception instanceof AuthenticationException => 401,$exception instanceof HttpExceptionInterface => $exception->getStatusCode(),default => 500
            };
            $message = match ($status) {
                422 => 'Validation failed',404 => 'Content not found',403 => 'Forbidden',401 => 'Unauthenticated',429 => 'Too many requests',405 => 'Method not allowed',default => 'Unable to complete the request'
            };
            $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

            return response()->json(['data' => null, 'message' => $message, 'errors' => $exception instanceof ValidationException ? $exception->errors() : null, 'meta' => (object) []], $status, $headers);
        });
    })->create();
