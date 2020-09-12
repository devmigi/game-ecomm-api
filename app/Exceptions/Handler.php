<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $exception, $request) {

            // Api error response
            $apiErrorResponse = [
                'message' => 'Server Error!',
                'code' => 500
            ];

            if ($exception instanceof NotFoundHttpException) {
                $apiErrorResponse['message'] = 'Invalid Url!';
                $apiErrorResponse['code'] = 404;
            }
            else if ($exception instanceof AuthenticationException) {
                $apiErrorResponse['message'] = 'Unauthenticated';
                $apiErrorResponse['code'] = 403;
            }

            // If debug mode is enabled
            if (config('app.debug')) {
                // Add the exception class name, message and stack trace to response
                $apiErrorResponse['exception'] = get_class($exception); // Reflection might be better here
                $apiErrorResponse['message'] = $exception->getMessage();
            }

            return response()->json($apiErrorResponse, $apiErrorResponse['code']);

        });
    }
}
