<?php
declare(strict_types=1);

namespace App\Common;

use Psr\Http\Message\ResponseInterface;

class ResponseHelper
{
    public static function withJSONPayload(ResponseInterface $response, $data): ResponseInterface {
        $response->getBody()->write((string) json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}