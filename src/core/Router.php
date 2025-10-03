<?php
namespace core;

class Router {
    private array $routes = [];

    public function get(string $path, string $controllerAction): void {
        $this->routes['GET'][$path] = $controllerAction;
    }

    public function post(string $path, string $controllerAction): void {
        $this->routes['POST'][$path] = $controllerAction;
    }

    public function patch(string $path, string $controllerAction): void {
        $this->routes['PATCH'][$path] = $controllerAction;
    }

    public function SeeRoutesSave(): array{
        return $this->routes;
    }

    public function dispatch(string $uri, string $method): void {
        $uri = parse_url($uri, PHP_URL_PATH);
    
        // remove o caminho base do projeto
        $basePath = PATH_BASE;
        if (str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
    
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }
    
        $action = null;
        $params = [];
    
        // Procurar rota que bate
        foreach ($this->routes[$method] ?? [] as $route => $controllerAction) {
            $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([0-9a-zA-Z_-]+)', $route);
            $pattern = "#^" . $pattern . "$#";
    
            if (preg_match($pattern, $uri, $matches)) {
                $action = $controllerAction;
                array_shift($matches); // remove match completo
                $params = $matches;
                break;
            }
        }
    
        // End-Point não existir
        if (!$action) {
            [$controller, $method] = explode('@', "controllers\Error404Controller@index");
            $controllerInstance = new $controller();
            call_user_func([$controllerInstance, $method]);
            return;
        }
    
        [$controller, $method] = explode('@', $action);
        $controllerInstance = new $controller();
    
        // Passa os parâmetros para o método
        call_user_func_array([$controllerInstance, $method], $params);
    }    

}