<?php

namespace App\Exceptions;

use App\Http\Resources\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
        $this->reportable(function (Throwable $e) {

        });
    }

    public function render($request, Throwable $e): Response|JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if (!$this->shouldReturnJson($request, $e)) {
            return parent::render($request, $e);
        }

        if ($e instanceof ValidationException) {
            return (new ApiResponse())
                ->setStatus(ApiResponse::STATUS_ERROR)
                ->setError($e->errors())
                ->setHttpCode(422)
                ->setCode($e->getCode())
                ->getResponse();
        }

        if ($e instanceof NotFoundHttpException) {
            return (new ApiResponse())
                ->setStatus(ApiResponse::STATUS_ERROR)
                ->setError('404 NOT FOUND')
                ->setHttpCode(404)
                ->setCode($e->getCode())
                ->getResponse();
        }

        if ($e instanceof ModelNotFoundException) {
            return (new ApiResponse())
                ->setStatus(ApiResponse::STATUS_ERROR)
                ->setError('Resource not found')
                ->setHttpCode(404)
                ->setCode($e->getCode())
                ->getResponse();
        }

        if ($e instanceof MissingAbilityException) {
            return (new ApiResponse())
                ->setStatus(ApiResponse::STATUS_ERROR)
                ->setError('Forbidden')
                ->setHttpCode(403)
                ->setCode($e->getCode())
                ->getResponse();
        }

        if ($e instanceof AuthenticationException) {
            return (new ApiResponse())
                ->setStatus(ApiResponse::STATUS_ERROR)
                ->setError(($e->getMessage()))
                ->setHttpCode(403)
                ->setCode($e->getCode())
                ->getResponse();
        }

        if ($e instanceof \Exception) {
            return (new ApiResponse())
                ->setStatus(ApiResponse::STATUS_ERROR)
                ->setError($e->getMessage())
                ->setHttpCode(400)
                ->setCode($e->getCode())
                ->getResponse();
        }

        return (new ApiResponse())
            ->setStatus(ApiResponse::STATUS_ERROR)
            ->setError($e->getMessage())
            ->setHttpCode(500)
            ->setCode($e->getCode())
            ->getResponse();
    }
}
