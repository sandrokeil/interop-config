<?php

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$class = 'InteropBench\\Config\\' . $argv[1];

if (!class_exists($class)) {
    echo sprintf('Class %s does not exists.', $class);
    exit(1);
}

/* @var $benchmarkClass \InteropBench\Config\BaseCase */
$benchmarkClass = new $class();
$benchmarkClass->classSetUp();

$probe = BlackfireProbe::getMainInstance();
$probe->enable();
$benchmarkClass->options();
$probe->disable();
