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
                    $result = array_merge(
                        $result,
                        $this->processLayerElements($givenNamespace, $contextChild)
                    );
                }
            }
        }

        return $result;
    }

    /**
     * @return array<string>
     */
    private function processLayerElements(string $givenNamespace, SimpleXMLElement $contextChild): array
    {
        $result = [];
        $namespaceFromConfig = (string) $contextChild->attributes()['name'];

        // include nested namespaces in result
        if (substr($namespaceFromConfig, -2) === '\*') {
            $namespaceFromConfig = substr($namespaceFromConfig, 0, -2);
            if (strpos($givenNamespace, $namespaceFromConfig) === 0) {
                foreach ($contextChild as $layer) {
                    $result[] = (string) $layer->attributes()['name'];
                }
            }
        // include only strict matched namespaces in result
        } elseif ($givenNamespace === $namespaceFromConfig) {
            foreach ($contextChild as $layer) {
                $result[] = (string) $layer->attributes()['name'];
            }
        }

        return $result;
    }
}
