<?php

namespace JsonApi\Models;

class Response
{
    public function __construct(
        private readonly int $statusCode,
        private readonly array $headers,
        private readonly array $body
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $header) {
            header($header);
        }

        echo json_encode($this->body);
    }

    public static function json(array $body, int $statusCode = 200): self
    {
        return new self(
            $statusCode,
            ['Content-Type: application/json'],
            $body
        );
    }

    public static function unauthorized(string $reason): self
    {
        return self::json(['message' => $reason], 401);
    }

    public static function tooManyRequests(string $reason): self
    {
        return self::json(['message' => $reason], 429);
    }
}
