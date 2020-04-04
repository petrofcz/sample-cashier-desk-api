<?php
declare(strict_types=1);

namespace App\Common;

use Psr\Http\Message\ResponseInterface;

class CommonResponseFactory
{
    public static function createValidationErrorResponse(ResponseInterface $response, string $message): ResponseInterface {
        return self::withApiResponse(
            $response->withStatus(422),
            $message
        );
    }

    public static function createNotFoundResponse(ResponseInterface $response): ResponseInterface {
        return $response->withStatus(404);
    }

    public static function createConflictResponse(ResponseInterface $response): ResponseInterface {
        return $response->withStatus(409);
    }

    public static function withApiResponse(ResponseInterface $response, string $message): ResponseInterface {
        return ResponseHelper::withJSONPayload(
            $response,
            [
                'message' => $message
            ]
        );
    }
}