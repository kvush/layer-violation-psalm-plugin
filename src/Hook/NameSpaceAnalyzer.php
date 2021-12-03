<?php

declare(strict_types=1);

namespace Kvush\LayerViolationPsalmPlugin\Hooks;

use Kvush\LayerViolationPsalmPlugin\Config;
use Kvush\LayerViolationPsalmPlugin\Issue\LayerDependencyViolation;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterFileAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFileAnalysisEvent;
use Psalm\StatementsSource;

class NameSpaceAnalyzer implements AfterFileAnalysisInterface
{
    /** @var StatementsSource */
    private static $statementSource;

    public static function afterAnalyzeFile(AfterFileAnalysisEvent $event): void
    {
        $config = Config::getInstance();
        $config->assertConfigExists();

        self::$statementSource = $event->getStatementsSource();

        foreach ($event->getStmts() as $stmt) {
            if ($stmt instanceof Namespace_) {
                self::analyzeNamespace($stmt);
            }
        }
    }

    private static function analyzeNamespace(Namespace_ $namespaceStmt): void
    {
        if (null === $namespaceStmt->name) {
            return;
        }

        $namespace = join('\\', $namespaceStmt->name->parts);
        foreach ($namespaceStmt->stmts as $stmt) {
            if ($stmt instanceof Use_) {
                self::analyzeUseForNamespace($stmt, $namespace);
            }
        }
    }

    private static function analyzeUseForNamespace(Use_ $useStmt, string $parentNamespace): void
    {
        $config = Config::getInstance();
        $useNamespace = implode('\\', $useStmt->uses[0]->name->parts);

        $acceptableNamespaces = $config->getAcceptableForLayer($parentNamespace);

        $isValid = false;
        foreach ($acceptableNamespaces as $acceptableNamespace) {
            if (strpos($useNamespace, $acceptableNamespace) === 0) {
                $isValid = true;
                break;
            }
        }

        if (false === $isValid) {
            IssueBuffer::accepts(
                new LayerDependencyViolation($parentNamespace, new CodeLocation(self::$statementSource, $useStmt)),
                self::$statementSource->getSuppressedIssues()
            );
        }
    }
}
