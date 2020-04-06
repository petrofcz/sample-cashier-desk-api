<?php

use Ramsey\Uuid\Uuid;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    const API_KEY_HEADER = 'X-API-KEY';

    public function amAuthenticated(string $apiKey = null) {
        $this->deleteHeader(self::API_KEY_HEADER);
        $this->haveHttpHeader(self::API_KEY_HEADER, $apiKey ?: '0imfnc8mVLWwsAawjYr4Rx-Af50DDqtlx');
    }

    public function createdPayment(string $amount = '20.00', $currency = 'CZK', \DateTimeInterface $dateTime = null, ?string $id = null): string {
        if(!$dateTime) {
            $dateTime = new \DateTimeImmutable();
        }
        if(!$id) {
            $id = Uuid::uuid1();
        }
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPOST('payments', [
            'id' => $id,
            'amount' => $amount,
            'currency' => $currency,
            'dateTime' => $dateTime->format(\DateTimeInterface::RFC3339)
        ]);
        return $id;
    }
}
