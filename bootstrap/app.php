<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions
            ->shouldRenderJsonWhen(function(Request $request) {
                return request()->expectsJson() || $request->is('api/*');
            })
            ->render(function (NotFoundHttpException $e, Request $request) {
                return response()->json([
                    'message' => 'Not found',
                ], Response::HTTP_NOT_FOUND);
            })
            ->render(function (ModelNotFoundException $e, Request $request) {
                return response()->json([
                    'message' => 'Not found',
                ], Response::HTTP_NOT_FOUND);
            });
    })->create();
