<?php

use CultuurNet\MediaDownloadManager\FileName\FileNameFactory;
use CultuurNet\MediaDownloadManager\OriginSystem\OriginSystem;
use CultuurNet\MediaDownloadManager\Parser\Parser;
use DerAlex\Silex\YamlConfigServiceProvider;
use Silex\Application;
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

$app['mdm.file_name_factory'] = $app->share(
    function() {
        return new FileNameFactory();
    }
);

$app['mdm.origin'] = $app->share(
    function (Application $app) {
        $parameters = array();

        foreach ($app['config']['source_url']['parameters'] as $key => $value) {
            $parameters[$key] = urlencode($value);
        }

        return new OriginSystem(
            Url::fromNative($app['config']['source_url']['base_url']),
                $parameters
        );
    }
);

$app['mdm.parser'] = $app->share(
    function (Application $app) {
        return new Parser($app['mdm.file_name_factory'],
            $app['mdm.origin']
        );
    }
);

return $app;
