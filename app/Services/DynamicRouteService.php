<?php

namespace App\Services;

use App\Models\ApiRoute;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DynamicRouteService
{
    public function registerRoutes(): void
    {
        $routes = ApiRoute::query()->get();

        foreach ($routes as $route) {
            $this->registerRoute($route);
        }
    }

    protected function registerRoute(ApiRoute $route): void
    {
        $method = strtolower($route->http_method);
        $path = "{$route->service_group}/{$route->route_name}";
        $middleware = $route->middleware ?? 'api';
        $controller_class = "App\\Http\\Controllers\\" . $middleware . "\\" . ucfirst($route->service_group) . "\\{$route->controller_name}";

        if (!class_exists($controller_class)) {
            Log::warning("Controller class not found: {$controller_class}");
            return;
        }

        if (!$route->is_active) {
            Route::$method($path, function () use ($route) {
                return response()->json([
                    'error' => 'Endpoint Unavailable',
                    'message' => 'This endpoint is currently offline or under maintenance.',
                    'status' => 'inactive'
                ], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
            });
            return;
        }

        Route::$method($path, [$controller_class, $route->method_name]);
    }
}
