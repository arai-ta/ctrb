<?php

require __DIR__.'/vendor/autoload.php';

# env
$env = new \Dotenv\Dotenv(__DIR__);
$env->load();

# logger
$logger = new \Monolog\Logger('CTRB');
$logger->pushHandler(new \Monolog\Handler\StreamHandler(
    __DIR__.'/app.log'
));
$GLOBALS['logger'] = $logger;

# main redis
$redis = new Predis\Client([
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT'),
]);
$GLOBALS['redis'] = $redis;

# session redis
$session = new Predis\Session\Handler(new Predis\Client(
    [
        'host' => getenv('REDIS_HOST'),
        'port' => getenv('REDIS_PORT'),
    ],
    [
        'prefix' => 'session:'
    ]
), [
    'gc_maxlifetime' => 60 * 60 * 24 * 14
]);
$session->register();
session_start() or die('SESSION ERROR');

function h($text) {
    return htmlspecialchars($text);
}
