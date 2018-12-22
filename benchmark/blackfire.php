<?php
/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/interop-config for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Sandro Keil
 * @license   http://github.com/sandrokeil/interop-config/blob/master/LICENSE.md New BSD License
 */

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
