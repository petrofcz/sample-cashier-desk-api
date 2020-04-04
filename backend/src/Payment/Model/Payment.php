<?php
namespace App\Payment\Model;

class Payment {

    /**
     * @var string
     * ID of the payment. Client must generate UUID for each payment.
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     * Date and time of the payment.
     */
    private $dateTime;

    /**
     * @var string
     * Decimal amount of the payment.
     */
    private $amount;

    /**
     * @var string
     * Currency code according to ISO 4217 format.
     */
    private $currency;

    /**
     * @var string
     * ID of the payment owner.
     */
    private $clientId;

    /**
     * @var \DateTimeImmutable
     * Datetime on which the payment was successfully saved in the system.
     */
    private $savedDateTime;

    /**
     * Payment constructor.
     * @param string $id
     * @param \DateTimeImmutable $dateTime
     * @param string $amount
     * @param string $currency
     * @param string $clientId
     * @param \DateTimeImmutable $savedDateTime
     */
    public function __construct(string $id, \DateTimeImmutable $dateTime, string $amount, string $currency, string $clientId, \DateTimeImmutable $savedDateTime)
    {
        $this->id = $id;
        $this->dateTime = $dateTime;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->clientId = $clientId;
        $this->savedDateTime = $savedDateTime;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getSavedDateTime(): \DateTimeImmutable
    {
        return $this->savedDateTime;
    }

}
