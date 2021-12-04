<?php

declare(strict_types=1);

namespace Kvush\LayerViolationPsalmPlugin\Tests;

use Kvush\LayerViolationPsalmPlugin\Config;
use Kvush\LayerViolationPsalmPlugin\Exception\NoConfig;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use SimpleXMLElement;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        $property = new ReflectionProperty(Config::class, "instance");
        $property->setAccessible(true);
        $property->setValue(null);
    }

    /**
     * @test
     */
    public function exception_if_no_config_while_assert_it(): void
    {
        $config = Config::getInstance();

        self::expectException(NoConfig::class);
        $config->assertConfigExists();
    }

    /**
     * @test
     */
    public function exception_if_no_config_while_getting_configurable_layers(): void
    {
        $config = Config::getInstance();

        self::expectException(NoConfig::class);
        $config->getAcceptableForLayer('some/name');
    }

    /**
     * @test
     */
    public function provide_configurable_layers_with_nested(): void
    {
        $xml = <<<xml
<?xml version="1.0" encoding="UTF-8"?>
<pluginClass>
    <context>
        <common>
            <acceptable name="JetBrains\PhpStorm" />
        </common>
    
        <layer name="App\Domain\ContextA\*">
            <acceptable name="App\Domain\ContextA\*" />
            <acceptable name="App\DateTime" />
            <acceptable name="App\EntityId" />
        </layer>
        
        <layer name="App\Domain\ContextB\*">
            <acceptable name="App\Domain\ContextB\*" />
            <acceptable name="App\EntityId" />
        </layer>
    </context>
</pluginClass>
xml;

        $simpleXmlConfig = new SimpleXMLElement($xml);
        $config = Config::getInstance();
        $config->initConfig($simpleXmlConfig);

        $actual = $config->getAcceptableForLayer('App\Domain\ContextA');
        self::assertEquals(
            [
                'JetBrains\PhpStorm',
                'App\Domain\ContextA\*',
                'App\DateTime',
                'App\EntityId'
            ],
            $actual
        );

        self::assertEquals(
            $config->getAcceptableForLayer('App\Domain\ContextA\Some\Model\Deeper\Inside'),
            $config->getAcceptableForLayer('App\Domain\ContextA')
        );

        $actual = $config->getAcceptableForLayer('App\Domain\ContextB');
        self::assertEquals(
            [
                'JetBrains\PhpStorm',
                'App\Domain\ContextB\*',
                'App\EntityId'
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function provide_configurable_layers_without_nested(): void
    {
        $xml = <<<xml
<?xml version="1.0" encoding="UTF-8"?>
<pluginClass>
    <context>
        <common>
            <acceptable name="JetBrains\PhpStorm" />
        </common>
    
        <layer name="App">
            <acceptable name="Symfony\*" />
        </layer>
        
        <layer name="App\Domain\ContextA">
            <acceptable name="App\Domain\ContextA" />
            <acceptable name="App\EntityId" />
        </layer>
    </context>
</pluginClass>
xml;

        $simpleXmlConfig = new SimpleXMLElement($xml);
        $config = Config::getInstance();
        $config->initConfig($simpleXmlConfig);

        $actual = $config->getAcceptableForLayer('App');
        self::assertEquals(
            [
                'JetBrains\PhpStorm',
                'Symfony\*',
            ],
            $actual
        );

        $actual = $config->getAcceptableForLayer('App\Domain\ContextA');
        self::assertEquals(
            [
                'JetBrains\PhpStorm',
                'App\Domain\ContextA',
                'App\EntityId'
            ],
            $actual
        );

        self::assertNotEquals(
            $config->getAcceptableForLayer('App\Domain\ContextA\Some\Model\Deeper\Inside'),
            $config->getAcceptableForLayer('App\Domain\ContextA')
        );
    }
}
