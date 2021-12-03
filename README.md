# Layer Violation Psalm Plugin

### Installation

```
composer require --dev kvush/layer-violation-psalm-plugin
vendor/bin/psalm-plugin enable kvush/layer-violation-psalm-plugin
```

### Features

- Detects layers dependency violation based on provided config
- Configuration can be split by multiple xml files


### Configuration

Simple configuration

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

                <layer name="App\Domain\ContextA">
                    <acceptable name="App\Domain\ContextA" />
                    <acceptable name="App\DateTime" />
                    <acceptable name="App\EntityId" />
                </layer>

                <layer name="App\Domain\ContextB">
                    <acceptable name="App\Domain\ContextB" />
                    <acceptable name="App\EntityId" />
                </layer>
            </context>
        </pluginClass>
    </plugins>
</psalm>
```

Extracted to arbitrary named xml files

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
            <xi:include href="path/to/contextA.xml"/>
            <xi:include href="path/to/contextB.xml"/>
        </pluginClass>
    </plugins>
</psalm>
```

contextA.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<context>
    <layer name="App\Domain\ContextA">
        <acceptable name="App\Domain\ContextA" />
        <acceptable name="App\DateTime" />
        <acceptable name="App\EntityId" />
    </layer>
</context>
```

contextB.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<context>
    <layer name="App\Domain\ContextB">
        <acceptable name="App\Domain\ContextB" />
        <acceptable name="App\EntityId" />
    </layer>
</context>
```