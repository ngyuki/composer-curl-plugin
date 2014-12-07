#!/usr/bin/env php
<?php
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('memory_limit', -1);

$home = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'home';
$cache = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cache';
putenv("COMPOSER_HOME=$home");
putenv("COMPOSER_CACHE_DIR=$cache");
chdir(__DIR__);

use Composer\Console\Application;
use Composer\Autoload\ClassLoader;

/* @var $loader ClassLoader */
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('ngyuki\\ComposerCurlPlugin\\', dirname(__DIR__) . '/src', true);

$application = new Application();
$application->run();
