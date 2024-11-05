<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = $this->getStatusCode($e);
                
                return response()->json([
                    'message' => $e->getMessage(),
                    'status_code' => $statusCode
                ], $statusCode);
            }
        });
    }

    /**
     * Get the HTTP status code based on the exception type.
     */
    private function getStatusCode(Throwable $e): int
    {
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        if (method_exists($e, 'getCode')) {
            $statusCode = $e->getCode();
            // Ensure it's a valid HTTP status code
            if ($statusCode >= 100 && $statusCode < 600) {
                return $statusCode;
            }
        }

        // Default status codes based on exception type
        return match (true) {
            $e instanceof AuthenticationException => 401,
            $e instanceof ModelNotFoundException => 404,
            $e instanceof NotFoundHttpException => 404,
            $e instanceof ValidationException => 422,
            default => 500,
        };
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            // Handle validation exceptions
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $exception->validator->errors(),
                    'status_code' => 422,
                ], 422);
            }

            // Handle authentication exceptions
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'status_code' => 401,
                ], 401);
            }

            // Handle not found exceptions
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Resource not found.',
                    'status_code' => 404,
                ], 404);
            }

            // Handle model not found exceptions
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Resource not found.',
                    'status_code' => 404,
                ], 404);
            }

            // Handle other exceptions
            $statusCode = method_exists($exception, 'getStatusCode') 
                ? $exception->getStatusCode() 
                : 500;

            return response()->json([
                'message' => $exception->getMessage(),
                'status_code' => $statusCode,
            ], $statusCode);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'message' => 'Unauthenticated.',
            'status_code' => 401,
        ], 401);
    }
}
