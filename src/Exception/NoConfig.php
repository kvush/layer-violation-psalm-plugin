<?php

declare(strict_types=1);

namespace Kvush\LayerViolationPsalmPlugin\Exception;

use RuntimeException;

class NoConfig extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('No Config for Layer Violation Plugin');
    }
}
