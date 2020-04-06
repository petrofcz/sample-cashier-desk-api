<?php
declare(strict_types=1);

namespace App\Payment\Actions;

use App\Auth\AuthMiddleware;
use App\Common\CommonResponseFactory;
use App\Common\ResponseHelper;
use App\Common\SlimActionHandlerInterface;
use App\Repository\PaymentsRepository;
use App\Payment\Mapping\PaymentMapper;
use App\Payment\Model\Payment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListPaymentsAction implements SlimActionHandlerInterface
{
    const PARAM_FROM_TIME = 'fromTime';

    protected PaymentsRepository $paymentsFacade;

    protected PaymentMapper $paymentMapper;


    public function __construct(PaymentsRepository $paymentsFacade, PaymentMapper $paymentMapper)
    {
        $this->paymentsFacade = $paymentsFacade;
        $this->paymentMapper = $paymentMapper;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();

        // Handle fromTime argument
        if(isset($params[self::PARAM_FROM_TIME])) {
            $fromTime = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, $params[self::PARAM_FROM_TIME]);
            if($fromTime === false) {
                return CommonResponseFactory::createValidationErrorResponse($response, 'Invalid \'fromTime\' parameter format given. Use RFC3339.');
            }
        } else {
            $fromTime = null;
        }

        $clientId = $request->getAttribute(AuthMiddleware::ATTR_CLIENT_ID);

        $payments = $this->paymentsFacade->getPayments($clientId, $fromTime);

        $data = array_map(function(Payment $payment) {
            return $this->paymentMapper->createDataFromEntity($payment);
        }, $payments);

        return ResponseHelper::withJSONPayload($response, $data);
    }
}