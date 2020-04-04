<?php
declare(strict_types=1);

namespace App\Auth;

use Psr\Http\Message\ServerRequestInterface;

interface RequestAuthenticatorInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return string|null Client ID. Null on authentication failure.
     */
    public function authenticate(ServerRequestInterface $request): ?string;
}