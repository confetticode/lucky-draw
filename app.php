<?php

use ConfettiCode\LuckyDraw\Application;

require __DIR__ . '/vendor/autoload.php';

$config = file_exists($configFile = __DIR__ . '/config.env.php') ? require $configFile : [
    'debug' => false,
];

$ignition = \Confetti\Ignition\Ignition::setUp();

$ignition->setDebug($config['debug']);

$app = new Application(realpath(__DIR__));

return $app;
