<?php
declare(strict_types=1);

namespace App\Repository;

class ApiKeyRepository
{
    public function getClientIdByApiKey(string $apiKey): ?string {

        // todo implement real authentication solution

        return md5($apiKey);
    }
}