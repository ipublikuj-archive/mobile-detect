<?php

declare(strict_types = 1);

define('DS', DIRECTORY_SEPARATOR);

require __DIR__ . DS . '..' . DS . '..' . DS . 'vendor' . DS . 'autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

// Create temporary directory
define('TEMP_DIR', __DIR__ . '/../tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
Tester\Helpers::purge(TEMP_DIR);
\Tracy\Debugger::$logDirectory = TEMP_DIR;

$_SERVER = array_intersect_key($_SERVER, array_flip(array(
	'PHP_SELF', 'SCRIPT_NAME', 'SERVER_ADDR', 'SERVER_SOFTWARE', 'HTTP_HOST', 'DOCUMENT_ROOT', 'OS', 'argc', 'argv')));
$_SERVER['REQUEST_TIME'] = 1234567890;
$_ENV = $_GET = $_POST = array();

function id($val)
{
	return $val;
}

function run(Tester\TestCase $testCase)
{
	$testCase->run();
}
