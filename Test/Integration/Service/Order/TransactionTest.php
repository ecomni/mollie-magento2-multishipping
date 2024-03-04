<?php
/**
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mollie\Multishipping\Test\Integration\Service\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Mollie\Multishipping\Service\Order\MultishippingTransaction;
use Mollie\Payment\Test\Integration\IntegrationTestCase;

class TransactionTest extends IntegrationTestCase
{
    public function testGeneratesTheCorrectRedirectUrlWhenMultishipping()
    {
        $orders = [
            $this->objectManager->create(OrderInterface::class)->setId(777),
            $this->objectManager->create(OrderInterface::class)->setId(888),
            $this->objectManager->create(OrderInterface::class)->setId(999),
        ];

        /** @var MultishippingTransaction $instance */
        $instance = $this->objectManager->create(MultishippingTransaction::class);
        $result = $instance->getRedirectUrl($orders, ['PAYMENT_TOKEN_TEST']);

        $this->assertStringContainsString('order_ids[0]=777', urldecode($result));
        $this->assertStringContainsString('order_ids[1]=888', urldecode($result));
        $this->assertStringContainsString('order_ids[2]=999', urldecode($result));
        $this->assertStringContainsString('payment_token=PAYMENT_TOKEN_TEST', urldecode($result));
        $this->assertStringContainsString('utm_nooverride=1', urldecode($result));
    }

    public function testThrowsAnExceptionWhenTheOrderListIsEmpty()
    {
        $orders = [];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The provided order array is empty');

        /** @var MultishippingTransaction $instance */
        $instance = $this->objectManager->create(MultishippingTransaction::class);
        $instance->getRedirectUrl($orders, 'PAYMENT_TOKEN_TEST');
    }
}
