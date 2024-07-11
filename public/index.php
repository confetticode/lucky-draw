<?php

$app = require __DIR__ . '/../app.php';

$app->handleRequest(
    \Symfony\Component\HttpFoundation\Request::createFromGlobals()
);
