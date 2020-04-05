<?php
declare(strict_types=1);

namespace App\Payment\Actions;

use App\Auth\AuthMiddleware;
use App\Common\CommonResponseFactory;
use App\Common\JSONDecoderMiddleware;
use App\Common\SlimActionHandlerInterface;
use App\Repository\PaymentsRepository;
use App\Payment\Mapping\MappingException;
use App\Payment\Mapping\PaymentMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AddPaymentAction implements SlimActionHandlerInterface
{
    /** @var PaymentsRepository */
    protected $paymentsRepository;

    /** @var PaymentMapper */
    protected $paymentMapper;

    public function __construct(PaymentsRepository $paymentsRepository, PaymentMapper $paymentMapper)
    {
        $this->paymentsRepository = $paymentsRepository;
        $this->paymentMapper = $paymentMapper;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getAttribute(JSONDecoderMiddleware::ATTR_INPUT_DATA, null);
        if($data === null) {
            throw new \RuntimeException('No parsing of input data was performed.');
        }

        $clientId = $request->getAttribute(AuthMiddleware::ATTR_CLIENT_ID);

        try {
            $payment = $this->paymentMapper->createEntityFromData($data, $clientId, new \DateTimeImmutable());
        } catch (MappingException $e) {
            return CommonResponseFactory::createValidationErrorResponse($response, $e->getMessage());
        }

        // Add location header
        $response = $response->withHeader('X-Location', $request->getUri() . '/' . $payment->getId());

        // Check if payment already exists
        $existingPayment = $this->paymentsRepository->getPaymentById($payment->getId());

        if($existingPayment) {
            if($existingPayment->getClientId() != $payment->getClientId()) {
                return CommonResponseFactory::createConflictResponse($response);
            }
            if(!$existingPayment->sameAs($payment)) {
                return CommonResponseFactory::createConflictResponse($response);
            }
            return $response->withStatus(200);
        }

        $result = $this->paymentsRepository->addPayment($payment);
        if(!$result) {
            // Adding failed (due to duplicity)
            return CommonResponseFactory::createConflictResponse($response);
        }

        return $response->withStatus(201);
    }
}