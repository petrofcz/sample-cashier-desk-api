<?php
declare(strict_types=1);

namespace App\Logging;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface TrafficLoggerInterface
{
    public function logRequest(ServerRequestInterface $request): void;

    public function logResponse(ResponseInterface $response): void;
}