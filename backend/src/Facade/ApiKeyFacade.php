<?php
declare(strict_types=1);

namespace App\Facade;

class ApiKeyFacade
{
    public function getClientIdByApiKey(string $apiKey): ?string {

        // todo implement real authentication solution

        return md5($apiKey);
    }
}