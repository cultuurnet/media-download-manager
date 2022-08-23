<?php

use CultuurNet\MediaDownloadManager\DestinationSystem\DestinationSystem;
use CultuurNet\MediaDownloadManager\Download\Downloader;
use CultuurNet\MediaDownloadManager\Fetcher\Fetcher;
use CultuurNet\MediaDownloadManager\FileName\FileNameFactory;
use CultuurNet\MediaDownloadManager\OriginSystem\OriginSystem;
use CultuurNet\MediaDownloadManager\Parser\Parser;
use DerAlex\Silex\YamlConfigServiceProvider;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Adapter\Local;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Silex\Application;
use Srmklive\Dropbox\Adapter\DropboxAdapter;
use Srmklive\Dropbox\Client\DropboxClient;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\Url;

$app = new Application();

if (!isset($appConfigLocation)) {
    $appConfigLocation =  __DIR__;
}
$app->register(new YamlConfigServiceProvider($appConfigLocation . '/config.yml'));

/**
 * Turn debug on or off.
 */
$app['debug'] = $app['config']['debug'] === true;

/**
 * Load additional bootstrap files.
 */
foreach ($app['config']['bootstrap'] as $identifier => $enabled) {
    if (true === $enabled) {
        require __DIR__ . "/bootstrap/{$identifier}.php";
    }
}

$app['log_handler_parser'] = $app->share(
    function (Application $app) {
        return new RotatingFileHandler(
            $app['config']['logging_folder'] . '/parser.log',
            365,
            Logger::DEBUG
        );
    }
);

$app['logger_parser'] = $app->share(
    function (Application $app) {
        return new Logger('importer', array($app['log_handler_parser']));
    }
);

$app['log_fetcher_handler_parser'] = $app->share(
    function (Application $app) {
        return new RotatingFileHandler(
            $app['config']['logging_folder'] . '/fetcher.log',
            365,
            Logger::DEBUG
        );
    }
);

$app['logger_fetcher_parser'] = $app->share(
    function (Application $app) {
        return new Logger('fetcher', array($app['log_fetcher_handler_parser']));
    }
);

$app['log_handler_destination'] = $app->share(
    function (Application $app) {
        return new RotatingFileHandler(
            $app['config']['logging_folder'] . '/destination.log',
            365,
            Logger::DEBUG
        );
    }
);

$app['logger_destination'] = $app->share(
    function (Application $app) {
        return new Logger('destination', array($app['log_handler_destination']));
    }
);

$app['mdm.fetcher'] = $app->share(
    function (Application $app) {
        return new Fetcher(
            $app['config']['source_url']['api_key'],
            $app['logger_fetcher_parser']
        );

    }
);

$app['mdm.file_name_factory'] = $app->share(
    function() {
        return new FileNameFactory();
    }
);

$app['mdm.origin'] = $app->share(
    function (Application $app) {
        $parameters = array();

        foreach ($app['config']['source_url']['parameters'] as $key => $value) {
            $parameters[$key] = $value;
        }

        return new OriginSystem(
            Url::fromNative($app['config']['source_url']['base_url']),
                $parameters
        );
    }
);

$app['mdm.downloader'] = $app->share(
    function() {
        return new Downloader();
    }
);

$app['mdm.destination'] = $app->share(
    function(Application $app) {
        $adapters = array();
        if($app['config']['destination']['local']['active']) {
            $localAdapter = new Local($app['config']['destination']['local']['folder']);
            array_push($adapters, $localAdapter);
        }
        if($app['config']['destination']['dropbox']['active']) {
            $client = new DropboxClient($app['config']['destination']['dropbox']['authorizationToken']);
            $dropboxAdapter = new DropboxAdapter($client);
            array_push($adapters, $dropboxAdapter);
        }
        if($app['config']['destination']['ftp']['active']) {
            $ftpAdapter = new Ftp(
                [
                'host' => $app['config']['destination']['ftp']['host'],
                'username' => $app['config']['destination']['ftp']['username'],
                'password' => $app['config']['destination']['ftp']['password'],
                ]
            );
            array_push($adapters, $ftpAdapter);
        }

        return new DestinationSystem(
            $app['mdm.downloader'],
            new StringLiteral($app['config']['destination']['default_folder']),
            $adapters,
            $app['logger_destination']
        );
    }
);

$app['mdm.parser'] = $app->share(
    function (Application $app) {
        return new Parser(
            $app['mdm.fetcher'],
            $app['mdm.file_name_factory'],
            $app['mdm.origin'],
            $app['mdm.destination'],
            $app['logger_parser']
        );
    }
);

return $app;
