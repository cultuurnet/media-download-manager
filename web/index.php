<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap.php';

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
));

/**
 * Allow to use services as controllers.
 */
$app->register(new ServiceControllerServiceProvider());

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html');
});

$app->get('/{variable}', function ($variable) use ($app) {
    return $app['twig']->render('index.html', array('variable' => $variable));
});

$app->post('/', function (Request $request) use ($app) {
    $tagfield = $request->get('tagfield');
    $createdfield = $request->get('createdfield');

    $command = '../bin/app.php mediadownloader ';
    $command .= ' --label \'' . $tagfield . '\' ';
    if (isset($createdfield) && !empty($createdfield)) {
        $command .=  ' --createdSince ' . $createdfield;
    }
    $command .= ' > /dev/null &';
    exec($command);

    return $app['twig']->render(
        'response.html',
        array(
            'tag' => $tagfield,
            'created' => $createdfield,
            'command' => $command)
    );
});

$app->register(new FormServiceProvider());

$app->run();
