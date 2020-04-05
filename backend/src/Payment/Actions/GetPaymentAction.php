<?php
declare(strict_types=1);

namespace App\Payment\Actions;

use App\Auth\AuthMiddleware;
use App\Common\CommonResponseFactory;
use App\Common\ResponseHelper;
use App\Common\SlimActionHandlerInterface;
use App\Payment\Model\PaymentIdValidator;
use App\Repository\PaymentsRepository;
use App\Payment\Mapping\PaymentMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetPaymentAction implements SlimActionHandlerInterface
{
	const PARAM_PAYMENT_ID = 'id';

	/** @var PaymentsRepository */
	protected $paymentsFacade;

	/** @var PaymentMapper */
	protected $paymentMapper;

	/** @var PaymentIdValidator */
	protected $paymentIdValidator;

	public function __construct(PaymentsRepository $paymentsFacade, PaymentMapper $paymentMapper, PaymentIdValidator $paymentIdValidator)
	{
		$this->paymentsFacade = $paymentsFacade;
		$this->paymentMapper = $paymentMapper;
		$this->paymentIdValidator = $paymentIdValidator;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
	{
		if(!isset($args[self::PARAM_PAYMENT_ID])) {
		    return CommonResponseFactory::createValidationErrorResponse($response, 'Payment ID must be set.');
		}

		$paymentId = $args[self::PARAM_PAYMENT_ID];

		if(!$this->paymentIdValidator->validate($paymentId)) {
            return CommonResponseFactory::createValidationErrorResponse($response, 'Payment ID must be a valid UUID.');
        }

		$clientId = $request->getAttribute(AuthMiddleware::ATTR_CLIENT_ID);

		$payment = $this->paymentsFacade->getPaymentById($paymentId);

		if(!$payment || $payment->getClientId() != $clientId) {
            return CommonResponseFactory::createNotFoundResponse($response);
        }

		return ResponseHelper::withJSONPayload($response, $this->paymentMapper->createDataFromEntity($payment));
	}
}