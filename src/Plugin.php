<?php

declare(strict_types=1);

namespace Kvush\LayerViolationPsalmPlugin;

use Kvush\LayerViolationPsalmPlugin\Hooks\NameSpaceAnalyzer;
use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

class Plugin implements PluginEntryPointInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null): void
    {
        foreach ($this->getStubFiles() as $file) {
            $psalm->addStubFile($file);
        }

        require_once 'Hook/NameSpaceAnalyzer.php';
        require_once 'Config.php';

        Config::getInstance()->initConfig($config);
        $psalm->registerHooksFromClass(NameSpaceAnalyzer::class);
    }

    /** @return list<string> */
    private function getStubFiles(): array
    {
        return glob(__DIR__ . '/stubs/*.phpstub') ?: [];
    }
}
