<?php

namespace JsonApi\Controller;

use JsonApi\Models\Request;
use JsonApi\Models\Response;

abstract class BaseController
{
    abstract public function canHandle(Request $request): bool;

    abstract public function handle(Request $request): Response;

    public function requiresAuth(): bool
    {
        return true;
    }
}
