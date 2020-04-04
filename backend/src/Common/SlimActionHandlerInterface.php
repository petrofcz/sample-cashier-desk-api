<?php
declare(strict_types=1);

namespace App\Common;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This is a base interface for Slim action handlers.
 */
interface SlimActionHandlerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface;
}