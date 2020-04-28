<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nette\Application\Application as Application;
use Contributte\Console\Application as Console;

$application = php_sapi_name() === 'cli' ? Console::class : Application::class;

App\Bootstrap::boot()
	->createContainer()
	->getByType($application)
	->run();
