<?php

namespace JsonApi\Models;

class ApiClientUser
{
    public function __construct(
        private readonly int $userId,
        private readonly int $clientId,
        private readonly string $apiKey,
        private readonly UserStatus $status
    ) {}

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }
}
