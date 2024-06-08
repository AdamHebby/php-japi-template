<?php

namespace JsonApi\Controller;

use JsonApi\Application\Query\GetRandomNumberQuery;
use JsonApi\Models\Request;
use JsonApi\Models\Response;

class HomeController extends BaseController
{
    public function canHandle(Request $request): bool
    {
        return $request->getMethod() === 'GET' && $request->getUri() === '/';
    }

    public function handle(Request $request): Response
    {
        return Response::json(['message' => 'OK']);
    }

    public function requiresAuth(): bool
    {
        return false;
    }
}
