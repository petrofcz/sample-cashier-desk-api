<?php
declare(strict_types=1);

namespace App\Common;

use App\Facade\ApiKeyFacade;
use Psr\Http\Message\ServerRequestInterface;

class ApiKeyRequestAuthenticator implements RequestAuthenticatorInterface
{
    const API_KEY_HEADER_NAME = 'X-API-KEY';

    /** @var ApiKeyFacade */
    protected $apiKeyFacade;

    /**
     * ApiKeyRequestAuthenticator constructor.
     * @param ApiKeyFacade $apiKeyFacade
     */
    public function __construct(ApiKeyFacade $apiKeyFacade)
    {
        $this->apiKeyFacade = $apiKeyFacade;
    }

    public function authenticate(ServerRequestInterface $request): ?string {
        if($request->hasHeader(self::API_KEY_HEADER_NAME)) {
            $headers = $request->getHeader(self::API_KEY_HEADER_NAME);
            if(count($headers) > 1) {
                return null;
            }
            return $this->apiKeyFacade->getClientIdByApiKey(array_shift($headers));
        }
        return null;
    }
}