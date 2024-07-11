<?php

namespace App\Core;


use Confetti\ErrorHandler\ErrorHandler;
use Confetti\ErrorHandler\SymfonyDisplayer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    private array $config = [];
    private bool $bootstrapped = false;
    private string $viewsPath;
    private ErrorHandler $errors;
    /** @var \Closure[] $requestHandlers */
    private array $requestHandlers = [];
    private \Closure $fallbackRequestHandler;

    public function __construct(private string $basePath)
    {
        $this->viewsPath = $this->basePath . '/resources/views';

        $this->fallbackRequestHandler = function ($app, $request) {
            throw new class extends \RuntimeException {
                public function getStatusCode(): int
                {
                    return Response::HTTP_NOT_FOUND;
                }
            };
        };
    }

    private function loadConfiguration(): void
    {
        $this->config = file_exists($configFile = $this->basePath . '/config.env.php') ? require $configFile : [
            'debug' => false,
        ];
    }

    private function startSession(): void
    {
        session_start();

        $generate =  function ($length = 32) {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
        };

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = call_user_func($generate);
        }
    }

    private function handleExceptions(): void
    {
        $this->errors = new ErrorHandler();

        $displayer = new SymfonyDisplayer($this->config['debug']);

        $this->errors->setDisplayer($displayer);

        $this->errors->register();
    }
    
    public function bootstrap(): void
    {
        if ($this->bootstrapped) {
            return;
        }

        $this->loadConfiguration();

        $this->startSession();

        $this->handleExceptions();

        $this->bootstrapped = true;
    }

    public function handleRequest(Request $request): void
    {
        $this->bootstrap();

        foreach ($this->requestHandlers as $handler) {
            $response = call_user_func($handler, $this, $request);

            if ($response instanceof Response) {
                $response->send();
                return;
            }
        }

        $response = call_user_func($this->fallbackRequestHandler, $this, $request);

        $response->send();
    }

    public function render($viewName, $data = []): Response
    {
        return $this->renderViewAsResponse($viewName, $data);
    }

    public function renderViewAsResponse($viewName, $data = []): Response
    {
        return new Response(
            $this->renderViewAsString($viewName, $data)
        );
    }

    public function renderViewAsString($viewName, $data = []): string
    {
        ob_start();

        extract($data);

        require $this->viewsPath . '/'.$viewName.'.php';

        return (string) ob_get_clean();
    }

    public function pushRequestHandler(\Closure $requestHandler): self
    {
        $this->requestHandlers[] = $requestHandler;

        return $this;
    }

    public function loadRoutes(string $name): self
    {
        $app = $this;

        require $this->basePath . '/routes/'.$name.'.php';

        return $this;
    }
}
