<?php

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

date_default_timezone_set('Europe/Prague');

Tester\Environment::setup();

if(extension_loaded('xdebug')) {
    Tester\CodeCoverage\Collector::start(__DIR__ . '/coverage.dat');
}

function id($val) {
    return $val;
}

function run(Tester\TestCase $testCase) {
    $testCase->run(isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : NULL);
}
