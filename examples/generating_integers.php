<?php

use Eris\Generators;

require __DIR__.'/../vendor/autoload.php';

$eris = new Eris\Facade();
$eris
    ->forAll(Generators::int())
    ->then(function ($integer) {
        echo var_export($integer, true) . PHP_EOL;
    });
