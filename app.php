<?php

use App\Core\Application;

require __DIR__.'/vendor/autoload.php';

$app = new Application(
    realpath(__DIR__)
);

$app->loadRoutes('lucky-draw');

return $app;
