<?php
declare(strict_types=1);

namespace App\Payment\Actions;

use App\Auth\AuthMiddleware;
use App\Common\CommonResponseFactory;
use App\Common\JSONDecoderMiddleware;
use App\Common\SlimActionHandlerInterface;
use App\Facade\PaymentsFacade;
use App\Payment\Mapping\MappingException;
use App\Payment\Mapping\PaymentMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Validator\GenericValidator;

class AddPaymentAction implements SlimActionHandlerInterface
{
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

        // add header
        $response = $response->withHeader('Location', $request->getUri() . '/' . $payment->getId());

        $existingPayment = $this->paymentsFacade->getPaymentById($payment->getId());
        if($existingPayment) {
            if($existingPayment->getClientId() != $payment->getClientId()) {
                return CommonResponseFactory::createConflictResponse($response);
            }
            if($this->paymentMapper->createDataFromEntity($existingPayment) != $this->paymentMapper->createDataFromEntity($payment)) {
                return CommonResponseFactory::createConflictResponse($response);
            }
            return $response->withStatus(200);
        }

        $this->paymentsFacade->addPayment($payment);

        return $response->withStatus(201);
    }
}