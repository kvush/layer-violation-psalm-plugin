<?php

declare(strict_types=1);

namespace Kvush\LayerViolationPsalmPlugin;

use Kvush\LayerViolationPsalmPlugin\Exception\NoConfig;
use SimpleXMLElement;

class Config
{
    /** @var null|Config  */
    private static $instance = null;
    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    /** @var SimpleXMLElement|null */
    private $config = null;

    public function initConfig(?SimpleXMLElement $config): void
    {
        if ($this->config === null) {
            $this->config = $config;
        }
    }

    /**
     * @throws NoConfig
     */
    public function assertConfigExists(): void
    {
        if ($this->config === null) {
            throw new NoConfig();
        }
    }

    /**
     * @param string $givenNamespace
     * @return array<string>
     */
    public function getAcceptableForLayer(string $givenNamespace): array
    {
        $result = [];

        $this->assertConfigExists();
        /** @var array<SimpleXMLElement>|SimpleXMLElement $context */
        $context = $this->config->context;

        /** @var array<SimpleXMLElement>|SimpleXMLElement $contextElement */
        foreach ($context as $contextElement) {
            foreach ($contextElement as $contextChild) {
                if ($contextChild->getName() === 'common') {
                    foreach ($contextChild as $common) {
                        $result[] = (string) $common->attributes()['name'];
                    }
                }
                if ($contextChild->getName() === 'layer') {
                    $namespaceFromConfig = (string) $contextChild->attributes()['name'];
                    if (strpos($givenNamespace, $namespaceFromConfig) === 0) {
                        foreach ($contextChild as $layer) {
                            $result[] = (string) $layer->attributes()['name'];
                        }
                    }
                }
            }
        }

        return $result;
    }
}
