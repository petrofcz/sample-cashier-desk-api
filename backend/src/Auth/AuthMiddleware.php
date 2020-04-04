<?php
declare(strict_types=1);

namespace App\Auth;

use App\Common\CommonResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    const ATTR_CLIENT_ID = 'clientId';

    /** @var RequestAuthenticatorInterface */
    protected $requestAuthenticator;

    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    public function __construct(RequestAuthenticatorInterface $requestAuthenticator, ResponseFactoryInterface $responseFactory)
    {
        $this->requestAuthenticator = $requestAuthenticator;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->requestAuthenticator->authenticate($request);
        if($result === null) {
            return CommonResponseFactory::withApiResponse(
                $response = $this->responseFactory->createResponse(401),
                'Invalid authentication. Please check your key / token.'
            );
        } else {
            return $handler->handle(
                $request->withAttribute(self::ATTR_CLIENT_ID, $result)
            );
        }
    }
}