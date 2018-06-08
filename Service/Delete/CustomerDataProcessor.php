<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Delete;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Model\Config;

/**
 * Class CustomerDataProcessor
 */
class CustomerDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Config $config
    ) {
        $this->customerRepository = $customerRepository;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $customerEmail): bool
    {
        //todo if config: delete customer if no orders
        try {
            $this->customerRepository->delete($this->customerRepository->get($customerEmail));
        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
