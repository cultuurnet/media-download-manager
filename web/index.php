<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap.php';

$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

/**
 * Allow to use services as controllers.
 */
$app->register(new ServiceControllerServiceProvider());

$app->get('/', function () {
    $output = '';
    $output .= '<form method="post">';
    $output .= 'label:&nbsp';
    $output .= '<input type="text" name="tagfield" value="owner-omd-2017">';
    $output .= '<br />';
    $output .= '<input type="submit" value="Download">';
    $output .= '</form>';

    return $output;
});

$app->post('/', function (Request $request) {
    $tagfield = $request->get('tagfield');
    exec('../bin/app.php mediadownloader --label '. $tagfield . ' > /dev/null &');
    return new Response('Pictures of events with label ' . $tagfield . ' will be downloaded to dropbox.', 201);
});

$app->register(new FormServiceProvider());

$app->run();
