<?php

namespace ConfettiCode\LuckyDraw;

use Faker\Factory as Faker;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    private string $viewsPath;

    public function __construct(private string $basePath)
    {
        $this->viewsPath = $this->basePath . '/resources/views';
    }

    public function handleRequest(): void
    {
        session_start();

        $items = $_SESSION['items'] ?? [];

        if (empty($items)) {
            $faker = Faker::create();

            $items = [];
            foreach (range(1, 50) as $i) {
                $name = str_replace("'", " ", $faker->unique()->name());
                $items[] = $name . ' - ' . $i;
            }
        }

        if (!is_array($items)) {
            $response = new Response('Whoops! Something went wrong', 500);

            $response->send();

            exit(1);
        }

        require $this->viewsPath . '/index.php';
    }
}
