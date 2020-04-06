<?php
declare(strict_types=1);

namespace App\Logging;

use App\Auth\AuthMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ResourceTrafficLogger implements TrafficLoggerInterface
{
    const REQ_PREFIX = "\t> ";
    const RES_PREFIX = "\t< ";

    /** @var resource STDOUT, file handle, .. */
    private $resource;

    private string $datetimeFormat = 'j.n.Y H:i:s';

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function logRequest(ServerRequestInterface $request): void
    {
        $lines = [
            (new \DateTime())->format($this->datetimeFormat),
            self::REQ_PREFIX . $request->getMethod() . ' ' . $request->getUri()
        ];
        if(($clientId = $request->getAttribute(AuthMiddleware::ATTR_CLIENT_ID))) {
            $lines[] = self::REQ_PREFIX . sprintf('Client ID: %s', $clientId);
        }
        $body = $request->getBody()->getContents();
        if($body) {
            $lines[] = self::REQ_PREFIX . $body;
        }
        fwrite($this->resource, implode("\n", $lines) . "\n\n");
    }

    public function logResponse(ResponseInterface $response): void
    {
        $lines = [
            self::RES_PREFIX . $response->getStatusCode() . ' ' . $response->getReasonPhrase()
        ];
        foreach($response->getHeaders() as $hdrName => $hdrVals) {
            foreach($hdrVals as $hdrVal) {
                $lines[] = self::RES_PREFIX . $hdrName . ': ' . $hdrVal;
            }
        }
        if($body = ((string) $response->getBody())) {
            $lines[] = self::RES_PREFIX . $body;
        }
        fwrite($this->resource, implode("\n", $lines) . "\n\n");
    }

    /**
     * @param string $datetimeFormat
     */
    public function setDatetimeFormat(string $datetimeFormat): void
    {
        $this->datetimeFormat = $datetimeFormat;
    }

}