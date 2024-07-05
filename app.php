<?php

use ConfettiCode\LuckyDraw\Application;

require __DIR__ . '/vendor/autoload.php';

$app = new Application(realpath(__DIR__));

return $app;
