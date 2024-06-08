<?php

use JsonApi\Bootstrapper;
use JsonApi\Controller\Handler;
use JsonApi\Models\Request;

require __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__ . '/..');

$bootstrapper = new Bootstrapper();
$handler = $bootstrapper
    ->useCli(false)
    ->boot()
    ->getContainer()
    ->get(Handler::class);

$request = Request::fromGlobals();

$response = $handler->handleRequest($request);

$handler->handleResponse($request, $response);
