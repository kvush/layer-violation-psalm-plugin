<?php

declare(strict_types=1);

namespace Kvush\LayerViolationPsalmPlugin\Issue;

use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;

class LayerDependencyViolation extends PluginIssue
{
    public function __construct(string $parentNamespace, CodeLocation $codeLocation)
    {
        parent::__construct(sprintf('Not allowed dependency for %s', $parentNamespace), $codeLocation);
    }
}
