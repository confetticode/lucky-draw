<?php

/** @var \App\Core\Application $app */

use App\Core\Application;
use Faker\Generator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

$app->pushRequestHandler(function (Application $app, Request $request) {
    if ($request->getPathInfo() !== '/lucky-draw') {
        return null;
    }

    if (! $request->isMethod('GET')) {
        return null;
    }

    $items = $_SESSION['items'] ?? [];

    if (empty($items)) {
        $faker = Generator::create();

        $items = [];
        foreach (range(1, 50) as $i) {
            $name = str_replace("'", " ", $faker->unique()->name());
            $items[] = $name . ' - ' . $i;
        }
    }

    if (!is_array($items)) {
        throw new \RuntimeException('$items must be a string array.');
    }

    return $app->render('index', ['items' => $items, 'csrfToken' => $_SESSION['csrf_token']]);
});

$app->pushRequestHandler(function (Application $app, Request $request) {
    if (trim($request->getPathInfo()) !== '/luck-draw') {
        return null;
    }

    if (! $request->isMethod('POST')) {
        return null;
    }

    $csrfToken = $_SESSION['csrf_token'];

    $payload = json_decode($request->getContent(), JSON_OBJECT_AS_ARRAY) ?? [];

    if (($payload['_token'] ?? null) !== $csrfToken) {
        throw new class ('Token Mismatched') extends \RuntimeException {
            public function getStatusCode(): int
            {
                return \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST;
            }
        };
    }

    if (isset($payload['items']) && is_array($items = $payload['items'])) {
        $_SESSION['items'] = $items;
    }

    return new JsonResponse([
        'message' => 'Settings saved successfully',
    ]);
});
