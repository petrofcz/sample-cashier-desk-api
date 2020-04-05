<?php
declare(strict_types=1);

namespace App\Logging;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TrafficLogMiddleware implements MiddlewareInterface
{
    /** @var TrafficLoggerInterface */
    protected $logger;

    /**
     * TrafficLogMiddleware constructor.
     * @param TrafficLoggerInterface $logger
     */
    public function __construct(TrafficLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->logRequest($request);
        $response = $handler->handle($request);
        $this->logger->logResponse($response);
        return $response;
    }
}