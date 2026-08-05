<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Format semua ValidationException di route /api/* menjadi format konsisten
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                // Ambil pesan error pertama dari semua field
                $firstMessage = collect($e->errors())->flatten()->first()
                    ?? 'Data tidak valid.';

                return response()->json([
                    'success' => false,
                    'message' => $firstMessage,
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Format 401 Unauthenticated
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi habis atau tidak terautentikasi. Silakan login kembali.',
                ], 401);
            }
        });

    })->create();
