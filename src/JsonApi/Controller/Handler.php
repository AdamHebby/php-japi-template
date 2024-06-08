<?php

namespace JsonApi\Controller;

use JsonApi\Infrastructure\Repository\ApiRepository;
use JsonApi\Models\ApiClientUser;
use JsonApi\Models\Request;
use JsonApi\Models\Response;
use JsonApi\Models\UserStatus;

class Handler
{
    /** @var BaseController */
    private array $controllers = [];

    private ?ApiClientUser $user = null;

    public function __construct(
        private readonly ApiRepository $apiRepository
    ) {
    }

    public function registerControllers(array $controllers): void
    {
        foreach ($controllers as $controller) {
            $this->controllers[] = $controller;
        }
    }

    public function handleRequest(Request $request): Response
    {
        foreach ($this->controllers as $controller) {
            if (!$controller->canHandle($request)) {
                continue;
            }

            if ($controller->requiresAuth()) {
                $apiKey = $request->getBearerToken();

                if (!$apiKey || empty(trim($apiKey))) {
                    return Response::unauthorized('Unauthorized - Missing API Key');
                }

                $this->user = $this->apiRepository->authoriseByApiKey($apiKey);

                if (!$this->user || $this->user->getStatus() !== UserStatus::ACTIVE) {
                    return Response::unauthorized('Unauthorized - User not found or inactive.');
                }

                if (!$this->apiRepository->checkUsageLimits($this->user->getUserId())) {
                    return Response::tooManyRequests('Too Many Requests');
                }
            }

            return $controller->handle($request);
        }

        return new Response(404, [], ['message' => 'Not Found']);
    }

    public function handleResponse(Request $request, Response $response): void
    {
        $response->send();

        if ($response->getStatusCode() !== 200) {
            return;
        }

        if ($this->user !== null) {
            $this->apiRepository->logUsage($this->user->getUserId(), $this->user->getClientId());
        }

        $this->user = null;
    }
}
