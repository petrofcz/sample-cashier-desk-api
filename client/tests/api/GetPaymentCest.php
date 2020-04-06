<?php

use Codeception\Util\HttpCode;
use Ramsey\Uuid\Uuid;

class GetPaymentCest
{
    public function _before(ApiTester $I)
    {
    }

    // tests
    public function authentication(ApiTester $I)
    {
        $I->sendGET(sprintf('payments/%s', Uuid::uuid1()));
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function invalidId(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->sendGET('payments/a');
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function notFound(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->sendGET(sprintf('payments/%s', Uuid::uuid1()));
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function ok(ApiTester $I)
    {
        $I->amAuthenticated();
        $paymentId = $I->createdPayment(
            $amount = '100',
            $currency = 'CZK',
            $dateTime = DateTimeImmutable::createFromMutable((new \DateTime())->sub(new DateInterval('PT10M')))
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->sendGET(sprintf('payments/%s', $paymentId));
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
        $I->seeResponseContainsJson([
            'id'        => $paymentId,
            'amount'    => $amount,
            'currency'  => $currency,
        ]);
    }

    public function owner(ApiTester $I) {
        $I->amAuthenticated();
        $firstPaymentId = $I->createdPayment();
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->sendGET(sprintf('payments/%s', $firstPaymentId));
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->amAuthenticated(Uuid::uuid1());
        $I->sendGET(sprintf('payments/%s', $firstPaymentId));
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

}
