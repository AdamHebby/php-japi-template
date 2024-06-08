<?php

namespace JsonApi\Models;

class Request
{
    public function __construct(
        private string $method,
        private string $uri,
        private array $headers,
        private array $body
    ) {
        $this->uri = rtrim($this->uri, '/') . '/';
        $this->uri = str_replace('/api', '', $this->uri);
    }

    public function getBearerToken(): ?string
    {
        $authorizationHeader = $this->headers['Authorization'] ?? null;

        if ($authorizationHeader === null) {
            return null;
        }

        return str_replace('Bearer ', '', $authorizationHeader);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public static function fromGlobals(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            getallheaders(),
            $_REQUEST
        );
    }
}
