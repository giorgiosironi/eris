<?php
use Eris\Generator;

require __DIR__.'/../vendor/autoload.php';

$eris = new Eris\Facade();
$eris
    ->forAll(Generator\int())
    ->then(function ($integer) {
        echo var_export($integer, true) . PHP_EOL;
    });
