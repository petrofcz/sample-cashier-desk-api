<?php
declare(strict_types=1);

namespace App\Common;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware class for validating and decoding JSON content
 */
class JSONDecoderMiddleware implements MiddlewareInterface
{
    const ATTR_INPUT_DATA = 'inputData';

    protected ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(in_array(strtolower($request->getMethod()), ['post', 'put', 'patch'])) {
            if($request->hasHeader('Content-Type')) {
                $headers = $request->getHeader('Content-Type');
                if(count($headers) > 1 || array_shift($headers) != 'application/json') {
                    return $this->responseFactory->createResponse(400);
                }
                $data = json_decode($request->getBody()->getContents(), true);
                if($data === false) {
                    return $this->responseFactory->createResponse(400);
                }
                $request = $request->withAttribute(self::ATTR_INPUT_DATA, $data);
            }
        }
        return $handler->handle($request);
    }
}