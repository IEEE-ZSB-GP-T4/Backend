<?php

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        // 401 - مش مسجل دخول / التوكن غلط أو منتهي
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::response(401, 'Unauthenticated');
            }
        });

        // 403 - ده اللي بيتفعل فعليًا من الـ Policy (authorize())
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::response(403, 'This action is unauthorized');
            }
        });

        // 404 - لما الـ route نفسه مش موجود، أو Route Model Binding مايلاقيش السجل
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::response(404, 'Resource not found');
            }
        });

        // 422 - لما الـ validation يفشل
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return ApiResponse::response(422, 'Validation failed', $e->errors());
            }
        });

        // أي حاجة تانية غير متوقعة (500) - آخر خط دفاع
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                if (config('app.debug')) {
                    return ApiResponse::response(500, $e->getMessage());
                }

                return ApiResponse::response(500, 'Something went wrong');
            }
        });

    })->create();