<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel
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

Общий список кодов:

200: OK. Стандартный код успешного ответа.
201: Объект создан. Полезен при работе с хранилищем(магазином).
204: Отсутствует контент. Когда действие выполнено успешно, но не возвращен контент.
206: Частичный контент. Используется когда вы  возвращаете контент постранично(пагинация).
400: Bad request. Стандартная опция для запросов которые не прошли валидацию.
401: Unauthorized. Пользователь не прошел авторизацию.
403: Forbidden. Пользователь авторизован, но у него не хватает прав для выполнения запроса.
404: Not found. возвращается Laravel автоматически когда запрошенный ресурс не найден.
500: Internal server error. В идеале, вы не должны возвращать такой ответ, но когда, что то неожиданно сбоит, то пользователь получит такой ответ.
503: Service unavailable. Довольно понятно, но тоже, код который не будет явно возращен приложением.
Отправка корректного 404
Если попытаетесь запросить несуществующий ресурс, то вы получите:

