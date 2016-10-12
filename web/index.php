<?php
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../demo/check.php';
require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../templates',
]);

$regex = [
    'dn'    => '/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/',
    'ns_ip' => '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',
    'ns_dn' => '/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/',
];

$app->get('/', function () use ($app) {
    return $app['twig']->render('base.html.twig');
});

$app->post('/query', function (Request $request) use ($app, $regex) {

    $data = json_decode($request->getContent(), true);
    if ($data === null || json_last_error() != 0) {
        return json_encode([
            'status'  => 500,
            'message' => 'Unable to load json',
        ]);
    }

    if (!preg_match($regex['dn'], $data['dn'])) {
        return json_encode([
            'status'  => 500,
            'message' => 'Domain Name failed security check',
        ]);
    }
    if (!preg_match($regex['ns_ip'], $data['ns']) AND !preg_match($regex['ns_dn'], $data['ns'])) {
        return json_encode([
            'status'  => 500,
            'message' => 'Nameserver failed security check',
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
