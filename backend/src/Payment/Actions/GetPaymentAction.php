<?php
declare(strict_types=1);

namespace App\Payment\Actions;

use App\Auth\AuthMiddleware;
use App\Common\CommonResponseFactory;
use App\Common\ResponseHelper;
use App\Common\SlimActionHandlerInterface;
use App\Facade\PaymentsFacade;
use App\Payment\Mapping\PaymentMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Validator\GenericValidator;

class GetPaymentAction implements SlimActionHandlerInterface
{
	const PARAM_PAYMENT_ID = 'id';

	/** @var PaymentsFacade */
	protected $paymentsFacade;

	/** @var PaymentMapper */
	protected $paymentMapper;

	/** @var GenericValidator */
	protected $uuidValidator;

	public function __construct(PaymentsFacade $paymentsFacade, PaymentMapper $paymentMapper, GenericValidator $uuidValidator)
	{
		$this->paymentsFacade = $paymentsFacade;
		$this->paymentMapper = $paymentMapper;
		$this->uuidValidator = $uuidValidator;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
	{
		if(!isset($args[self::PARAM_PAYMENT_ID])) {
		    return CommonResponseFactory::createValidationErrorResponse($response, 'Payment ID must be set.');
		}

		$paymentId = $args[self::PARAM_PAYMENT_ID];

		if(!$this->uuidValidator->validate($paymentId)) {
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