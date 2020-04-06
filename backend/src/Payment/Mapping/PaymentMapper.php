<?php
declare(strict_types=1);

namespace App\Payment\Mapping;

use App\Payment\Model\Payment;
use App\Payment\Model\PaymentIdValidator;


/**
 * This class provides transformation between entity and its API representation
 */
class PaymentMapper
{
    protected PaymentIdValidator $paymentIdValidator;

    public function __construct(PaymentIdValidator $paymentIdValidator)
    {
        $this->paymentIdValidator = $paymentIdValidator;
    }

    public function createDataFromEntity(Payment $payment) {
        return [
            'id'        =>  $payment->getId(),
            'amount'    =>  $payment->getAmount(),
            'currency'  =>  $payment->getCurrency(),
            'dateTime'  =>  $payment->getDateTime()->format(\DateTimeInterface::RFC3339)
        ];
    }

    /**
     * @param array $data
     * @param string $clientId
     * @param \DateTimeInterface $currentDateTime
     * @return Payment
     * @throws MappingException
     */
    public function createEntityFromData(array $data, string $clientId, \DateTimeInterface $currentDateTime): Payment {
        if(!isset($data['id'])) {
            throw new MappingException('Payment ID must be set.');
        }
        if(!isset($data['amount'])) {
            throw new MappingException('Amount of the payment must be set.');
        }
        if(!isset($data['currency'])) {
            throw new MappingException('Currency of the payment must be set.');
        }
        if(!isset($data['dateTime'])) {
            throw new MappingException('Date and time of the payment must be set.');
        }
        if(($paymentDateTime = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, $data['dateTime'])) === false) {
            throw new MappingException('Payment date and time must be formatted according to RFC3339.');
        }
        if($paymentDateTime > (new \DateTime())) {
            throw new MappingException('Payment cannot be placed in future.');
        }
        if(!preg_match('/^\d{1,10}(\.\d{1,3})?$/', $data['amount']) || (((float)$data['amount']) == 0)) {
            throw new MappingException('Amount of the payment must be positive and must contain max 3 decimal digits. Ex: 230.900, 0.5, 120');
        }
        if(!preg_match('/^[A-Z]{3}$/', $data['currency'])) {
            throw new MappingException('Currency code must be valid according to ISO 4217 format.');
        }
        if(!$this->paymentIdValidator->validate($data['id'])) {
            throw new MappingException('Payment ID must be a valid UUID.');
        }
        return new Payment(
            $data['id'],
            $paymentDateTime,
            $data['amount'],
            $data['currency'],
            $clientId,
            \DateTimeImmutable::createFromFormat('U', $currentDateTime->format('U'))
        );
    }
}