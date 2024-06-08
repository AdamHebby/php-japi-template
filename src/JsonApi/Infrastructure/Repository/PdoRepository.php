<?php

namespace JsonApi\Infrastructure\Repository;

abstract class PdoRepository implements RepositoryInterface
{
    public function __construct(
        protected readonly \PDO $pdo
    ) {
    }
}
