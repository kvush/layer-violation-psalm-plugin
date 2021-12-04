# Layer Violation Psalm Plugin
[![Latest Stable Version](http://poser.pugx.org/kvush/layer-violation-psalm-plugin/v)](https://packagist.org/packages/kvush/layer-violation-psalm-plugin) [![Total Downloads](http://poser.pugx.org/kvush/layer-violation-psalm-plugin/downloads)](https://packagist.org/packages/kvush/layer-violation-psalm-plugin) [![Latest Unstable Version](http://poser.pugx.org/kvush/layer-violation-psalm-plugin/v/unstable)](https://packagist.org/packages/kvush/layer-violation-psalm-plugin) [![License](http://poser.pugx.org/kvush/layer-violation-psalm-plugin/license)](https://packagist.org/packages/kvush/layer-violation-psalm-plugin) [![PHP Version Require](http://poser.pugx.org/kvush/layer-violation-psalm-plugin/require/php)](https://packagist.org/packages/kvush/layer-violation-psalm-plugin)
## Installation

```
composer require --dev kvush/layer-violation-psalm-plugin
vendor/bin/psalm-plugin enable kvush/layer-violation-psalm-plugin
```

## Features

- Detects layers dependency violation based on provided config
- Configuration can be split by multiple xml files
- Ability to configure nested namespaces or keep strict match


## Configuration

### Simple configuration

```xml
<?xml version="1.0"?>
<psalm
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns="https://getpsalm.org/schema/config"
    xsi:schemaLocation="https://getpsalm.org/schema/config vendor/vimeo/psalm/config.xsd"
    totallyTyped="true"
>
    <!--  project configuration -->

    <plugins>
        <pluginClass class="Kvush\LayerViolationPsalmPlugin\Plugin">
            <context>
                <common>
                    <acceptable name="JetBrains\PhpStorm" />
                </common>
                
                <layer name="App">
                    <acceptable name="Symfony" />
                </layer>

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
    </plugins>
</psalm>
```

### Extracted to arbitrary named xml files

```xml
<?xml version="1.0"?>
<psalm
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns="https://getpsalm.org/schema/config"
    xsi:schemaLocation="https://getpsalm.org/schema/config vendor/vimeo/psalm/config.xsd"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    totallyTyped="true"
>
    <!--  project configuration -->

    <plugins>
        <pluginClass class="Kvush\LayerViolationPsalmPlugin\Plugin">
            <xi:include href="path/to/root.xml"/>
            <xi:include href="path/to/common.xml"/>
            <xi:include href="path/to/contextA.xml"/>
            <xi:include href="path/to/contextB.xml"/>
        </pluginClass>
    </plugins>
</psalm>
```

#### For Example Kernel class could be handled by `root.xml`
```xml
<?xml version="1.0" encoding="UTF-8"?>
<layer name="App">
    <acceptable name="Symfony" />
</layer>
```
#### Some Common rules in `common.xml`
```xml
<?xml version="1.0" encoding="UTF-8"?>
<common>
    <acceptable name="JetBrains\PhpStorm" />
</common>
```

#### Split context rules `contextA.xml`
```xml
<?xml version="1.0" encoding="UTF-8"?>
<context>
    <layer name="App\Domain\ContextA\*">
        <acceptable name="App\Domain\ContextA\*" />
        <acceptable name="App\DateTime" />
        <acceptable name="App\EntityId" />
    </layer>
</context>
```

`contextB.xml`
```xml
<?xml version="1.0" encoding="UTF-8"?>
<context>
    <layer name="App\Domain\ContextB\*">
        <acceptable name="App\Domain\ContextB\*" />
        <acceptable name="App\EntityId" />
    </layer>
</context>
```
