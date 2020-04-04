<?php

namespace App\Payment\Slim;

use App\Auth\AuthMiddleware;
use App\Common\SlimConfiguratorInterface;
use App\Payment\Actions\AddPaymentAction;
use App\Payment\Actions\GetPaymentAction;
use App\Payment\Actions\ListPaymentsAction;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * This class set up routes for Slim app to accept payment-related REST API requests.
 */
class PaymentSlimConfigurator implements SlimConfiguratorInterface
{
    public function register(App $app) {
        $app->group('/payments',  function (RouteCollectorProxy $group) {
            $group->get('', ListPaymentsAction::class);
            $group->get(sprintf('/{%s}', GetPaymentAction::PARAM_PAYMENT_ID), GetPaymentAction::class);
            $group->post('', AddPaymentAction::class);
        })
            ->add(AuthMiddleware::class);
    }
}