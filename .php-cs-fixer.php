<?php

$finder = PhpCsFixer\Finder::create()->in(__DIR__);

$conf = new \PhpCsFixer\Config();
$conf->setRules([
    '@PSR2' => true,
]);
$conf->setFinder($finder);

return $conf;
