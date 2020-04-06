<?php

use Codeception\Util\HttpCode;

class ListPaymentsCest
{
    public function _before(ApiTester $I)
    {
    }

    // tests
    public function authentication(ApiTester $I)
    {
        $I->sendGET('payments');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function invalidFromTime(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->sendGET('payments', ['fromTime' => (new DateTime())->format('Y-m-d H:i:s')]);
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function validFromTime(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->sendGET('payments', ['fromTime' => (new DateTime())->sub(new DateInterval('PT5M'))->format(DateTimeInterface::RFC3339)]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function fromTime(ApiTester $I)
    {
        $I->amAuthenticated();

        $startTime = new \DateTime();

        $paymentId = $I->createdPayment();
        $I->seeResponseCodeIs(HttpCode::CREATED);

        sleep(2);

        $I->sendGET('payments', ['fromTime' => $startTime->format(DateTimeInterface::RFC3339)]);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'id' => $paymentId
            ]
        ]);
    }

    public function list(ApiTester $I) {
        $I->amAuthenticated();
        $I->createdPayment();
        $I->sendGET('payments');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$[0].id');
    }
}
