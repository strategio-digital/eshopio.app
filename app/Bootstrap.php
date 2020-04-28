<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App;

use Nette\Configurator;
use Symfony\Component\Dotenv\Dotenv;

class Bootstrap
{
    public static function boot() : Configurator
	{
        $dotenv = new Dotenv;
        $dotenv->loadEnv(__DIR__ . '/../.env');
        $_ENV['NETTE_DEBUG'] = $_ENV['NETTE_DEBUG'] === "1";
        $_ENV['NETTE_CATCH_EXCEPTIONS'] = $_ENV['NETTE_CATCH_EXCEPTIONS'] === '1';

        $configurator = new Configurator;
        $configurator->addDynamicParameters($_ENV);
        $configurator->setDebugMode($_ENV['NETTE_DEBUG']);
        $configurator->setTimeZone('Europe/Prague');

        $configurator->enableTracy(__DIR__ . '/../log');
        $configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator
			->addConfig(__DIR__ . '/config/app.neon')
			->addConfig(__DIR__ . '/config/doctrine.neon');

		return $configurator;
	}
}
