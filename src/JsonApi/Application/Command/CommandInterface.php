<?php

namespace JsonApi\Application\Command;

interface CommandInterface
{
    public const SUCCESS = 0;
    public const ERROR = 1;

    public function execute(): int;
}