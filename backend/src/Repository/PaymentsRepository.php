<?php
declare(strict_types=1);

namespace App\Repository;

use App\Payment\Model\Payment;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use MongoDB\Database;

class PaymentsRepository
{
    const COLLECTION = 'payments';

    protected Collection $collection;

    public function __construct(Database $database)
    {
        $this->collection = $database->selectCollection(self::COLLECTION);
    }

    /**
     * Returns single payment by its ID
     * @param string $id ID of the payment.
     * @return Payment|null Desired payment if found.
     */
    public function getPaymentById(string $id): ?Payment {
        $result = $this->collection->findOne(['_id' => $id]);
        if ($result === null) {
            return null;
        }
        return $this->createEntity((array) $result);
    }

    /**
     * Returns all or latest payments belonging to given client.
     * @param string $clientId
     * @param \DateTimeInterface|null $timeFrom If null, all payments are returned. If set, only payments that were saved after given datetime (inclusive) will be returned.
     * @return Payment[] Desired payments
     */
    public function getPayments(string $clientId, ?\DateTimeInterface $timeFrom = null): array {
        $criteria = [
            'clientId'  => $clientId
        ];
        if($timeFrom) {
            $criteria['savedDateTime'] = ['$gte' => $this->createMongoTime($timeFrom)];
        }
        $results = $this->collection->find($criteria);
        return array_map(function($result) {
           return $this->createEntity($result);
        }, $results->toArray());
    }

    public function addPayment(Payment $payment): bool {
        $result = $this->collection->insertOne(
            $this->createData($payment)
        );
        if(!$result->isAcknowledged() || !$result->getInsertedCount()) {
            return false;
        }
        return true;
    }

    protected function createEntity($result): Payment
    {
        return new Payment(
            $result['_id'],
            $this->createDateTime($result['dateTime']),
            $result['amount'],
            $result['currency'],
            $result['clientId'],
            $this->createDateTime($result['savedDateTime'])
        );
    }

    protected function createData(Payment $payment): array {
        return [
            '_id'       =>  $payment->getId(),
            'dateTime'  =>  $this->createMongoTime($payment->getDateTime()),
            'amount'    =>  $payment->getAmount(),
            'currency'  =>  $payment->getCurrency(),
            'clientId'  =>  $payment->getClientId(),
            'savedDateTime' =>  $this->createMongoTime($payment->getSavedDateTime())
        ];
    }

    protected function createMongoTime(\DateTimeInterface $dateTime): UTCDateTime {
        return new UTCDateTime($dateTime->getTimestamp() * 1000);
    }

    protected function createDateTime(UTCDateTime $savedDateTime): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable($savedDateTime->toDateTime());
    }
}