<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/test',
        __DIR__ . '/examples',
    ]);

    // Skip vendor and generated files
    $rectorConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/src/Generator/first_names.txt',
        // Skip RemoveUnusedPrivateMethodRector because it can't detect
        // methods called via call_user_func with dynamic strings/arrays
        RemoveUnusedPrivateMethodRector::class,
    ]);

    // PHP 8.1 is the minimum version
    $rectorConfig->phpVersion(80100);

    // Import PHP version sets - upgrade to PHP 8.1
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
    ]);

    // Additional specific rules for modernization
    $rectorConfig->rules([
        InlineConstructorDefaultToPropertyRector::class,
        AddVoidReturnTypeWhereNoReturnRector::class,
    ]);
};
