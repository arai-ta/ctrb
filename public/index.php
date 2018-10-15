<?php

require __DIR__.'/../vendor/autoload.php';

$env = new \Dotenv\Dotenv(__DIR__.'/..');
$env->load();

$logger = new \Monolog\Logger('CTRB');
$logger->pushHandler(new \Monolog\Handler\StreamHandler(
    __DIR__.'/../app.log'
));

$GLOBALS['logger'] = $logger;

$redis = new Predis\Client([
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT'),
]);

$GLOBALS['redis'] = $redis;

$app = new Ctrb\App;
$app->execute();
