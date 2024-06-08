<?php

namespace JsonApi\Controller;

use JsonApi\Application\Query\GetRandomNumberQuery;
use JsonApi\Models\Request;
use JsonApi\Models\Response;

class TestController extends BaseController
{
    public function __construct(public readonly GetRandomNumberQuery $getRandomNumberQuery)
    {
    }

    public function canHandle(Request $request): bool
    {
        return $request->getMethod() === 'GET' && $request->getUri() === '/test/';
    }

    public function handle(Request $request): Response
    {
        return Response::json(['message' => $this->getRandomNumberQuery->execute(1, 100)]);
    }
}
