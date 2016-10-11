<?php
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../templates',
]);

$app->get('/', function () use ($app) {
    return $app['twig']->render('base.html.twig');
});

$app->post('/query', function (Request $request) use ($app) {

    $data = json_decode($request->getContent(), true);
    if ($data === null || json_last_error() != 0) {
        return json_encode([
            'status'  => 500,
            'message' => 'Unable to load json',
        ]);
    }

    if (!function_exists('exec')) {
        return json_encode([
            'status'  => 500,
            'message' => 'Function shell_exec must be available',
        ]);
    }

    $time = microtime(true);
    $response = [];
    $cmd = sprintf('host -a %s %s', $data['dn'], $data['ns']);
    exec($cmd, $response);
    $time = microtime(true) - $time;

    return json_encode([
        'status'  => 200,
        'message' => $response,
        'time'    => $time,
    ]);
});

$app->run();
