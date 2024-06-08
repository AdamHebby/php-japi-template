<?php

namespace JsonApi\Infrastructure\Repository;

use JsonApi\Models\ApiClientUser;
use JsonApi\Models\UserStatus;

final class ApiRepository extends PdoRepository
{
    public function authoriseByApiKey(string $apiKey): ApiClientUser
    {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT user.id, user.status, api_client.id, api_client.api_key FROM api_client
            RIGHT JOIN user ON api_client.user_id = user.id
                WHERE api_client.api_key = :api_key
                AND user.status = "active"
        SQL);

        $stmt->execute(['api_key' => $apiKey]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return new ApiClientUser(
            $result['id'],
            $result['id'],
            $result['api_key'],
            UserStatus::from($result['status'])
        );
    }

    public function checkUsageLimits(int $userId): bool
    {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT SUM(hits) as hits, l.limit FROM api_usage u
                RIGHT JOIN api_usage_limit l on u.user_id = l.user_id
                WHERE u.user_id = :user_id
                GROUP BY u.user_id, l.user_id;
        SQL);

        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['hits'] < $result['limit'];
    }

    public function logUsage(int $userId, int $apiClientId): void
    {
        $stmt = $this->pdo->prepare(<<<SQL
            INSERT INTO api_usage (user_id, api_client_id, hits)
                VALUES (:user_id, :api_client_id, 1) ON DUPLICATE KEY UPDATE hits = hits + 1
        SQL);

        $stmt->execute(['user_id' => $userId, 'api_client_id' => $apiClientId]);
    }
}
