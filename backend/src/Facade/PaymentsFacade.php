<?php
declare(strict_types=1);

namespace App\Facade;

use App\Payment\Model\Payment;

class PaymentsFacade
{
    /**
     * Returns single payment by its ID
     * @param string $id ID of the payment.
     * @return Payment|null Desired payment if found.
     */
    public function getPaymentById(string $id): ?Payment {

    }

    /**
     * Returns all or latest payments belonging to given client.
     * @param string $clientId
     * @param \DateTimeInterface|null $timeFrom If null, all payments are returned. If set, only payments that were saved after given datetime (inclusive) will be returned.
     * @return Payment[] Desired payments
     */
    public function getPayments(string $clientId, ?\DateTimeInterface $timeFrom = null): array {

    }

    /**
     * @param Payment $payment Saves new payment to the system.
     */
    public function addPayment(Payment $payment) {

    }
}