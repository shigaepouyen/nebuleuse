<?php
// /app/lib/Router.php

class Router {
    private $routes = [];

    public function add(string $method, string $path, $handler, bool $protected = false) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'protected' => $protected
        ];
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            $pattern = "#^" . $route['path'] . "$#";
            if ($requestMethod === $route['method'] && preg_match($pattern, $requestUri, $matches)) {
                
                if ($route['protected'] && !Auth::isLoggedIn()) {
                    header('Location: /login');
                    exit();
                }

                array_shift($matches); // remove the full match
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func_array($handler, $matches);
                } elseif (is_array($handler) && class_exists($handler[0]) && method_exists($handler[0], $handler[1])) {
                    $controller = new $handler[0]();
                    call_user_func_array([$controller, $handler[1]], $matches);
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}