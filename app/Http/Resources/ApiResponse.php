<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    protected string $status;
    protected mixed $error;
    protected mixed $message;
    protected mixed $data;
    protected int $httpCode;
    protected int $code;
    protected mixed $pagination;

    const STATUS_ERROR = 'ERROR';
    const STATUS_OK = 'OK';

    public function __construct()
    {
        $this->status = self::STATUS_OK;
        $this->httpCode = 200;
        $this->code = 0;
        $this->data = null;
        $this->pagination = null;
        $this->message = null;
    }

    public function getStatus(): string
    {
        return $this->status;
    }


    public function setStatus($status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getError(): mixed
    {
        return $this->error;
    }

    public function setError($error): static
    {
        $this->error = $error;
        $this->setStatus(self::STATUS_ERROR);
        $this->setHttpCode(400);
        return $this;
    }


    public function getMessage(): mixed
    {
        return $this->message;
    }

    public function setMessage($message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData($data): static
    {
        $this->data = $data;
        return $this;
    }

    public function getPagination(): mixed
    {
        return $this->pagination;
    }

    public function setPagination($paginated, $filters = []): static
    {
        $this->pagination = [
            'current_page' => (int)$paginated->currentPage(),
            'last_page' => (int)$paginated->lastPage(),
            'next_page_url' => $paginated->appends($filters)->nextPageUrl(),
            'previous_page_url' => $paginated->appends($filters)->previousPageUrl(),
            'per_page' => (int)$paginated->perPage(),
            'count' => (int)$paginated->count(),
            'total' => (int)$paginated->total()
        ];

        return $this;
    }

    public function getCode(): mixed
    {
        return $this->code;
    }

    public function setCode($code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function setHttpCode(int $httpCode): static
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    public function getResponse(): JsonResponse
    {
        if ($this->getStatus() === self::STATUS_ERROR) {

            return response()->json([
                'status' => $this->getStatus(),
                'code' => $this->getCode(),
                'message' => $this->getMessage(),
                'error' => $this->getError(),
                'data' => $this->getData(),
                'timestamp' => Carbon::now()->toIso8601String(),
            ], $this->getHttpCode());

        } else {

            $payload = [
                'status' => $this->getStatus(),
                'code' => $this->getCode(),
                'data' => $this->getData(),
                'message' => $this->getMessage(),
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            if ($this->getPagination() !== null) {
                $payload['pagination'] = $this->getPagination();
            }

            return response()->json($payload, $this->getHttpCode());
        }
    }
}
