<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mollie\Multishipping\Observer\MollieCheckoutSuccessRedirect;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Multishipping\Model\Checkout\Type\Multishipping\State;

class RedirectToMultishippingSuccessPage implements ObserverInterface
{
    /**
     * @var State
     */
    private $state;

    public function __construct(
        State $state
    ) {
        $this->state = $state;
    }

    public function execute(Observer $observer)
    {
        if (count($observer->getData('order_ids')) === 1) {
            return;
        }

        $this->state->setCompleteStep(State::STEP_OVERVIEW);
        $this->state->setActiveStep(State::STEP_SUCCESS);

        $observer->getData('redirect')->setData('path', 'multishipping/checkout/success?utm_nooverride=1');
    }
}
