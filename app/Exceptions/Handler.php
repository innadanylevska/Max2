<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    public const MESSAGE_VAR = 'message';

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
     * Report or log an exception.
     *
     * @param \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  \Exception  $exception
     * @return Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api', 'api/*') || $request->wantsJson()) {
            $message = $exception->getMessage();
            $response = [
                'success' => false,
                self::MESSAGE_VAR => $message,
            ];

            if ($exception instanceof ValidationException) {
                $message = collect($exception->errors())->flatten()->first();
                $statusCode = 422;
            } elseif ($exception instanceof UnauthorizedHttpException) {
                $message = $message ?: 'Unauthorized';
                $statusCode = 401;
            } elseif ($exception instanceof AuthenticationException) {
                $message = $message ?: 'Unauthenticated';
                $statusCode = 401;

            } elseif ($exception instanceof AuthorizationException) {
                $message = $message ?: 'Action not allowed';
                $statusCode = 403;
            } elseif ($exception instanceof ModelNotFoundException) {
                $message = 'Record Not Found';
                $statusCode = 404;
            } elseif ($exception instanceof NotFoundHttpException) {
                $message = 'Page Not Found';
                $statusCode = 404;
            } elseif ($exception instanceof MethodNotAllowedHttpException) {
                //                $message = $message ?: 'Method Not Allowed';
                $statusCode = 405;
            } else {
                $statusCode = method_exists($exception, 'getStatusCode') ?
                    $exception->getStatusCode() : 500;
                // more info in debug mode

                if (config('app.debug')) {
                    $response['type'] = class_basename($exception);
                    $response['file'] = $exception->getFile();
                    $response['line'] = $exception->getLine();
                    $response['trace'] = $exception->getTrace() ?: explode(PHP_EOL, $exception->getTraceAsString());
                }
            }

            $response[self::MESSAGE_VAR] = $message;

            return response()->json($response, $statusCode);
        }

        return parent::render($request, $exception);
    }
}
