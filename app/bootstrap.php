<?php

require __DIR__ . '/../vendor/autoload.php';

use Tracy\Debugger;

Tracy\Debugger::$productionMode = TRUE;
Tracy\Debugger::enable(FALSE);

// absolute filesystem path to the web root
define('WWW_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "www");

// absolute filesystem path to the application root
define('APP_DIR', dirname(__FILE__));

$configurator = new Nette\Configurator;

$configurator->setDebugMode(TRUE);

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');



Tracy\Debugger::$maxDepth = 5;
//Tracy\Debugger::$maxLen = 1;

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
//$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
