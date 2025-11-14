<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void {
        // 404 Model Not Found
        $this->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json([
                'error' => 'The requested resource was not found.'
            ], 404);
        });

        // 404 Wrong URL
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'error' => 'Endpoint not found.'
            ], 404);
        });

        // 405 Wrong HTTP Method
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json([
                'error' => 'HTTP method not allowed for this endpoint.'
            ], 405);
        });

        // 422 Validation Error
        $this->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'error' => 'Validation failed.',
                'details' => $e->errors(),
            ], 422);
        });

        // Fallback for all other exceptions
        $this->renderable(function (Throwable $e, $request) {
            return response()->json([
                'error' => 'Something went wrong. Please try again.',
            ], 500);
        });
    }
}
