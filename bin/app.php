#!/usr/bin/env php
<?php

use CultuurNet\MediaDownloadManager\Console\FetchCommand;
use Knp\Provider\ConsoleServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var \Silex\Application $app */
$app = require __DIR__ . '/../bootstrap.php';

$app->register(
    new ConsoleServiceProvider(),
    [
        'console.name'              => 'MediaDownloadManager',
        'console.version'           => '0.0.1',
        'console.project_directory' => __DIR__.'/..'
    ]
);

/** @var \Knp\Console\Application $consoleApp */
$consoleApp = $app['console'];

$consoleApp->add(
    new FetchCommand(
        $app['mdm.parser']
    )
);

$consoleApp->run();
