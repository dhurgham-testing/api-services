<?php

namespace App\Services;

use App\Models\ApiRoute;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class DynamicRouteService
{
    public function registerRoutes(): void
    {
        $routes = ApiRoute::query()->where('is_active', true)->get();

        foreach ($routes as $route) {
            $this->registerRoute($route);
        }
    }

    protected function registerRoute(ApiRoute $route): void
    {
        $method = strtolower($route->http_method);
        $path = "{$route->service_group}/{$route->route_name}";
        $controller_class = "App\\Http\\Api\\" . ucfirst($route->service_group) . "\\Controllers\\{$route->controller_name}";

        if (!class_exists($controller_class)) {
            Log::warning("Controller class not found: {$controller_class}");
            return;
        }

        Route::$method($path, [$controller_class, $route->method_name]);
    }
}
