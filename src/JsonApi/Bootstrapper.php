<?php

namespace JsonApi;

use DI\Container;
use JsonApi\Application\Command\TestCommand;
use JsonApi\Application\Query\GetRandomNumberQuery;
use JsonApi\Controller\Handler;
use JsonApi\Controller\HomeController;
use JsonApi\Controller\TestController;
use JsonApi\Infrastructure\Repository\ApiRepository;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\LogRecord;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Dotenv\Dotenv;

class Bootstrapper
{
    private Container $container;
    private bool $isUsingCommandLine = false;
    private bool $isBooted = false;

    public function __construct()
    {
        $this->container = new \DI\Container();
    }

    public function useCli(bool $isUsingCommandLine): self
    {
        $this->isUsingCommandLine = $isUsingCommandLine;

        return $this;
    }

    public function boot(): self
    {
        $this->loadDotEnv();
        $this->registerLogger();
        $this->registerQuery();
        $this->registerCommand();
        $this->registerDatabase();
        $this->registerControllers();

        $this->isBooted = true;

        return $this;
    }

    public function run(string $commandClass): int
    {
        if (!$this->isBooted) {
            throw new \Exception('Bootstrapper not booted');
        }

        $command = $this->container->get($commandClass);

        return $command->execute();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    private function loadDotEnv(): void
    {
        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__ . '/../../.env');
    }

    private function registerDatabase(): void
    {
        $this->container->set(\PDO::class, function () {
            return new \PDO(
                'mysql:host=' . $_ENV['MYSQL_HOST'] . ';dbname=' . $_ENV['MYSQL_DATABASE'],
                $_ENV['MYSQL_USER'],
                $_ENV['MYSQL_PASSWORD']
            );
        });

        $this->container->set(ApiRepository::class, function (\DI\Container $container) {
            return new ApiRepository($container->get(\PDO::class));
        });
    }

    private function registerControllers(): void
    {
        $this->container->set(Handler::class, function (\DI\Container $container) {
            $handler = new Handler($container->get(ApiRepository::class));

            $handler->registerControllers([
                new TestController($container->get(GetRandomNumberQuery::class)),
                new HomeController(),
            ]);

            return $handler;
        });
    }

    private function registerLogger(): void
    {
        $logger = new Logger('json-api');

        if ($this->isUsingCommandLine) {
            $formatter = new LineFormatter(
                "[%datetime%] %channel% %level_name% [CLI]: %message% %context% %extra%\n",
                'Y-m-d H:i:s'
            );
        } else {
            $formatter = new LineFormatter(
                "[%datetime%] %channel% %level_name% [%extra.request_path%]: %message% %context% %extra%\n",
                'Y-m-d H:i:s',
                false,
                true
            );

            $logger->pushProcessor(function (LogRecord $record) {
                $record['extra']['request_path'] = $_SERVER['REQUEST_URI'] ?? '/';

                return $record;
            });
        }

        $handlers = [
            new StreamHandler('logs/application.log', LogLevel::INFO),
            new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, LogLevel::ERROR)
        ];

        if ($this->isUsingCommandLine) {
            $handlers[] = new StreamHandler('php://stdout', LogLevel::DEBUG, true);
        }

        foreach ($handlers as $handler) {
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);
        }

        $this->container->set(LoggerInterface::class, $logger);
    }

    private function registerQuery(): void
    {
        $this->container->set(GetRandomNumberQuery::class, new GetRandomNumberQuery());
    }

    private function registerCommand(): void
    {
        $this->container->set(TestCommand::class, new TestCommand(
            $this->container->get(LoggerInterface::class),
            $this->container->get(GetRandomNumberQuery::class)
        ));
    }
}
