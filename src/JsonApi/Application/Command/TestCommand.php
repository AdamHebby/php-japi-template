<?php

namespace JsonApi\Application\Command;

use JsonApi\Application\Query\GetRandomNumberQuery;
use Psr\Log\LoggerInterface;

class TestCommand implements CommandInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly GetRandomNumberQuery $getRandomNumberQuery
    ) {
    }

    public function execute(): int
    {
        $this->logger->info('Random Number: ' . $this->getRandomNumberQuery->execute(1, 100));

        return self::SUCCESS;
    }
}
