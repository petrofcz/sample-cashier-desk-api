<?php

use Codeception\Util\HttpCode;

class CreatePaymentCest
{
    public function _before(ApiTester $I)
    {
    }

    // tests
    public function authentication(ApiTester $I)
    {
        $I->createdPayment();
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function paymentNegativeAmount(ApiTester $I)
    {
        $I->amAuthenticated();
        $paymentId = $I->createdPayment(
            '-1',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function zeroPayment(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '0',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }    
    
    public function zeroDecPayment(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '0.00',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function oneAmount(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '1',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
    }

    public function locationHeader(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '0.5',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeHttpHeaderOnce('X-Location');
    }
    
    public function integerAmount(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '200',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
    }

    public function dec1(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '200.0',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
    }
    
    public function dec2(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '200.00',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
    }
    
    public function dec3(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '200.000',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
    }

    public function dec4(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '200.0000',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }
    
    public function dec0(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '200.',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function stringAmount(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            'a',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }
    
    public function emptyAmount(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            '',
            'CZK',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function currencyLength(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            $amount = '1',
            $currency = 'a',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function emptyCurrency(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            $amount = '1',
            $currency = '',
            $this->generatePaymentTime()
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function dateInFuture(ApiTester $I)
    {
        $I->amAuthenticated();
        $I->createdPayment(
            $amount = '1',
            $currency = 'CZK',
            $dateTime = DateTimeImmutable::createFromMutable((new \DateTime())->add(new DateInterval('PT10M')))
        );
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
    }

    public function conflict(ApiTester $I) {
        $I->amAuthenticated();
        $paymentId = $I->createdPayment((string)($amount = 40), $currency = 'CZK', $dateTime = new \DateTimeImmutable());
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->createdPayment((string)($amount + 10), $currency, $dateTime, $paymentId);
        $I->seeResponseCodeIs(HttpCode::CONFLICT);
    }

    public function conflictOwner(ApiTester $I) {
        $I->amAuthenticated();
        $paymentId = $I->createdPayment((string)($amount = 40), $currency = 'CZK', $dateTime = new \DateTimeImmutable());
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->amAuthenticated(Ramsey\Uuid\Uuid::uuid1());
        $I->createdPayment((string)($amount), $currency, $dateTime, $paymentId);
        $I->seeResponseCodeIs(HttpCode::CONFLICT);
    }

    public function testExtraPush(ApiTester $I) {
        $I->amAuthenticated();
        $paymentId = $I->createdPayment($amount = 40, $currency = 'CZK', $dateTime = new \DateTimeImmutable());
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->amAuthenticated();
        $I->createdPayment($amount, $currency, $dateTime, $paymentId);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    protected function generatePaymentTime()
    {
        return DateTimeImmutable::createFromMutable((new \DateTime())->sub(new DateInterval('PT10M')));
    }

}
