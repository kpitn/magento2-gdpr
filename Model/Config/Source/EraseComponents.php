<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\Phrase;

use function array_keys;
use function array_map;
use function array_merge;

class EraseComponents implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(
        private ConfigInterface $objectManagerConfig,
        private string $factoryClassName
    ) {}

    public function toOptionArray(): array
    {
        return $this->options ??= array_map(
            static fn (string $delegateProcessor): array => [
                'value' => $delegateProcessor,
                'label' => new Phrase($delegateProcessor)
            ],
            $this->retrieveDelegateProcessors()
        );
    }

    /**
     * @return string[]
     */
    private function retrieveDelegateProcessors(): array
    {
        $resolvers = $this->retrieveArgument($this->factoryClassName, 'processorResolvers', []);

        $processors = [];
        foreach ($resolvers as $resolver) {
            $processors = array_merge($processors, $this->retrieveArgument($resolver, 'processors'));
        }

        return array_keys($processors);
    }

    private function retrieveArgument(string $className, string $argumentName, mixed $defaultValue = null): mixed
    {
        $arguments = $this->objectManagerConfig->getArguments(
            $this->objectManagerConfig->getPreference($className)
        );

        // Retrieve the argument by trying different keys in a prioritized order
        $keys = ['_i_', '_ins_', '_v_', '_vac_', '_vn_', '_a_', '_d_', 'instance', 'argument'];

        foreach ($keys as $key) {
            if (isset($arguments[$argumentName][$key])) {
                return $arguments[$argumentName][$key];
            }
        }

        return $arguments[$argumentName] ?? $defaultValue;
    }
}
