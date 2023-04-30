<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        // $container['db']=function($c){
        //     $settings = $c->get('settings')['db'];
        //     $pdo=new PDO("mysql:host=".$settings['host'].";dbname=".$settings['dbname'],
        //     $settings['users'], $settings['pass']);
        //     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //     $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
        //     return $pdo;
        // }
        PDO::class => function (ContainerInterface $c) {

            $settings = $c->get(SettingsInterface::class);

            $dbSettings = $settings->get('db');

            $host = $dbSettings['host'];
            $dbname = $dbSettings['database'];
            $username = $dbSettings['username'];
            $password = $dbSettings['password'];
            // $charset = $dbSettings['charset'];
            $flags = $dbSettings['flags'];
            $dsn = "mysql:host=$host;dbname=$dbname";
            return new PDO($dsn, $username, $password);
        },

    ]);
};
