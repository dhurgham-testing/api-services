<?php

namespace App\Services;

use App\Models\ApiRoute;
use Illuminate\Support\Facades\Route;

class DynamicRouteService
{
    public function registerRoutes(): void
    {
        // Get all active routes from database
        $routes = ApiRoute::query()->where('is_active', true)->get();


        foreach ($routes as $route) {
            $this->registerRoute($route);
        }
    }

    protected function registerRoute(ApiRoute $route): void
    {
        $method = strtolower($route->http_method);
        $path = "{$route->service_group}/{$route->route_name}";
        $controllerClass = "App\\Http\\Api\\" . ucfirst($route->service_group) . "\\Controllers\\{$route->controller_name}";

        // Check if controller class exists
        if (!class_exists($controllerClass)) {
            \Log::warning("Controller class not found: {$controllerClass}");
            return;
        }

        // Register the route dynamically
        Route::$method($path, [$controllerClass, $route->method_name]);
    }

    public function getRoutesByServiceGroup(string $serviceGroup): \Illuminate\Database\Eloquent\Collection
    {
        return ApiRoute::where('service_group', $serviceGroup)
            ->where('is_active', true)
            ->get();
    }

    public function isRouteActive(string $serviceGroup, string $routeName): bool
    {
        return ApiRoute::query()->where('service_group', $serviceGroup)
            ->where('route_name', $routeName)
            ->where('is_active', true)
            ->exists();
    }
}
