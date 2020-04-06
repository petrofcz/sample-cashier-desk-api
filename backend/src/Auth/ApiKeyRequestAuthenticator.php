<?php
declare(strict_types=1);

namespace App\Auth;

use App\Repository\ApiKeyRepository;
use Psr\Http\Message\ServerRequestInterface;

class ApiKeyRequestAuthenticator implements RequestAuthenticatorInterface
{
    const API_KEY_HEADER_NAME = 'X-API-KEY';

    protected ApiKeyRepository $apiKeyRepository;

    public function __construct(ApiKeyRepository $apiKeyFacade)
    {
        $this->apiKeyRepository = $apiKeyFacade;
    }

    public function authenticate(ServerRequestInterface $request): ?string {
        if($request->hasHeader(self::API_KEY_HEADER_NAME)) {
            $headers = $request->getHeader(self::API_KEY_HEADER_NAME);
            if(count($headers) > 1) {
                return null;
            }
            return $this->apiKeyRepository->getClientIdByApiKey(array_shift($headers));
        }
        return null;
    }
}