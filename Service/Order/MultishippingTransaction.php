<?php
/**
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mollie\Multishipping\Service\Order;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Mollie\Payment\Config;
use Mollie\Payment\Model\Adminhtml\Source\WebhookUrlOptions;

class MultishippingTransaction
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Config $config,
        Context $context
    ) {
        $this->config = $config;
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * @param OrderInterface[] $orderList
     * @param array $paymentTokens
     *
     * @return string
     *@throws \Exception
     */
    public function getRedirectUrl(array $orderList, array $paymentTokens): string
    {
        if (!$orderList) {
            throw new \Exception('The provided order array is empty');
        }

        $firstOrder = reset($orderList);
        $storeId = $firstOrder->getStoreId();

        $orderIds = array_map(function (OrderInterface $order) { return $order->getEntityId(); }, $orderList);
        $parameters = http_build_query([
            'order_ids' => $orderIds,
            'payment_tokens' => $paymentTokens,
            'utm_nooverride' => 1,
        ]);

        $this->urlBuilder->setScope($storeId);
        return $this->urlBuilder->getUrl(
            'mollie/checkout/process/',
            ['_query' => $parameters]
        );
    }

    /**
     * @param null|int|string $storeId
     * @return string
     */
    public function getWebhookUrl($storeId = null)
    {
        if ($this->config->isProductionMode($storeId) ||
            $this->config->useWebhooks($storeId) == WebhookUrlOptions::ENABLED) {
            return $this->urlBuilder->getUrl('mollie/checkout/webhook/', ['_query' => 'isAjax=1']);
        }

        if ($this->config->useWebhooks($storeId) == WebhookUrlOptions::DISABLED) {
            return '';
        }

        return $this->config->customWebhookUrl($storeId);
    }
}
