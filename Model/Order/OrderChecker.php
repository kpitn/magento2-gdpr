<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order;

use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Entity\EntityCheckerInterface;

use function in_array;

class OrderChecker implements EntityCheckerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private Config $config
    ) {}

    public function canErase(int $orderId): bool
    {
        $order = $this->orderRepository->get($orderId);

        return in_array($order->getState(), $this->config->getAllowedStatesToErase(), true);
    }
}
