<?php

namespace JsonApi\Application\Query;

/**
 * Gets a random number between the given min and max values.
 *
 * This is just an example, a query would usually interact with a repository or some other data source.
 *
 * The purpose of a query is to retrieve data in a repeatable manner.
 */
class GetRandomNumberQuery implements QueryInterface
{
    public function execute(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
