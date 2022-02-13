<?php

namespace RTC\Http\Router\Routing;

use Closure;
use JsonSerializable;
use RTC\Contracts\Http\Router\RouteInterface;
use RTC\Http\Router\Route;
use ValueError;

class TheRoute implements RouteInterface, JsonSerializable
{
    protected string $prefix = '';
    protected string $namespace = '';
    protected array $middlewares = [];
    protected string $name = '';
    protected string $append = '';
    protected string $prepend = '';
    protected string $method = '';
    protected array $fields = [];
    protected array $parameterTypes = [
        'number' => [],
        'alpha' => [],
        'alphanumeric' => [],
        'regExp' => [],
    ];
    protected Closure $group;
    protected TheRoute $parentRoute;

    /**
     * @var mixed Route handler/handler
     */
    protected mixed $action = null;


    public function __construct(?TheRoute $parentRoute = null)
    {
        if (null !== $parentRoute) {
            $this->parentRoute = $parentRoute;
        }
    }


    public function head(string $route, callable|array|string $action): static
    {
        return $this->addRoute('HEAD', $route, $action);
    }

    /**
     * Add route
     * @param string $method
     * @param string $route
     * @param callable|array|string $action
     * @return $this
     */
    protected function addRoute(string $method, string $route, callable|array|string $action): static
    {
        $this->method = $method;
        $this->prefix = $route;
        $this->action = $action;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function match(array $methods, string $uri, callable|array|string $action): static
    {
        foreach ($methods as $method) {
            $method = strtolower($method);
            $route = new TheRoute($this);
            $route->name(strtolower($method));
            Route::push($route);
            $route->$method($uri, $action);
        }

        return $this;
    }

    public function any(array $paths, string $method, callable|array|string $action): static
    {
        foreach ($paths as $path) {
            $method = strtolower($method);
            $route = new TheRoute($this);
            Route::push($route);
            $route->$method($path, $action);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function matchAny(array $methods, array $paths, callable|array|string $action): static
    {
        foreach ($methods as $method) {
            foreach ($paths as $path) {
                $method = strtolower($method);
                $route = new TheRoute($this);
                Route::push($route);
                $route->$method($path, $action);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resource(
        string $uri,
        string $controller,
        string $idParameterName = 'id',
        bool   $integerId = true
    ): static
    {
        $idParam = $integerId
            ? '{' . "$idParameterName:[0-9]+" . '}'
            : '{' . "$idParameterName" . '}';

        //  GET /whatever
        $route = new TheRoute($this);
        $route->get($uri, [$controller, 'index'])->name('index');
        Route::push($route);

        //  GET /whatever/create
        $route = new TheRoute($this);
        $route->get("$uri/create", [$controller, 'create'])->name('create');
        Route::push($route);

        //  POST /whatever
        $route = new TheRoute($this);
        $route->post($uri, [$controller, 'store'])->name('store');
        Route::push($route);

        //  GET /whatever/{$id}
        $route = new TheRoute($this);
        $route->get("$uri/$idParam", [$controller, 'show'])->name('show');
        Route::push($route);

        //  GET /whatever/{$id}/edit
        $route = new TheRoute($this);
        $route->get("$uri/$idParam/edit", [$controller, 'edit'])->name('edit');
        Route::push($route);

        //  PUT /whatever/{$id}
        $route = new TheRoute($this);
        $route->put("$uri/$idParam", [$controller, 'update'])->name('update');
        Route::push($route);

        //  PATCH /whatever/{$id}
        $route = new TheRoute($this);
        $route->patch("$uri/$idParam", [$controller, 'update'])->name('update');
        Route::push($route);

        //  DELETE /whatever/{$id}
        $route = new TheRoute($this);
        $route->delete("$uri/$idParam", [$controller, 'destroy'])->name('destroy');
        Route::push($route);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $route, callable|array|string $action): static
    {
        return $this->addRoute('GET', $route, $action);
    }

    /**
     * @inheritDoc
     */
    public function post(string $route, callable|array|string $action): static
    {
        return $this->addRoute('POST', $route, $action);
    }

    /**
     * @inheritDoc
     */
    public function put(string $route, callable|array|string $action): static
    {
        return $this->addRoute('PUT', $route, $action);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $route, callable|array|string $action): static
    {
        return $this->addRoute('PATCH', $route, $action);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $route, callable|array|string $action): static
    {
        return $this->addRoute('DELETE', $route, $action);
    }

    /**
     * @inheritDoc
     */
    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function append(string $prefix): static
    {
        $this->append = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prepend(string $prefix): static
    {
        $this->prepend = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function group(Closure $closure): static
    {
        $this->group = $closure;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function namespace(string $namespace): static
    {
        if ($namespace[strlen($namespace) - 1] !== "\\") {
            $namespace .= "\\";
        }
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function middleware(array|string $middleware): static
    {
        if (is_array($middleware)) {
            $this->middlewares = array_merge($this->middlewares, $middleware);
            return $this;
        }

        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addField(string $name, mixed $value): static
    {
        $this->fields[$name] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function where(array|string $parameter, ?string $regExp = null): static
    {
        if (is_array($parameter)) {
            $this->parameterTypes['regExp'] = array_merge($this->parameterTypes['regExp'], $parameter);
        } else {
            if (null === $regExp) {
                throw new ValueError('Second parameter must not be null when string is passed to first parameter.');
            }
            $this->parameterTypes['regExp'] += [$parameter => $regExp];
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereNumber(string $param): static
    {
        $this->parameterTypes['number'][] = $param;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereAlpha(string $param): static
    {
        $this->parameterTypes['alpha'][] = $param;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereAlphaNumeric(string $param): static
    {
        $this->parameterTypes['alphanumeric'][] = $param;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->getData();
    }

    public function getData(): array
    {
        $this->onRegister();

        $routeData = [
            'prefix' => $this->prefix,
            'namespace' => $this->namespace,
            'handler' => $this->action,
            'middleware' => $this->middlewares,
            'method' => $this->method,
            'name' => $this->name,
            'prepend' => $this->prepend,
            'append' => $this->append,
            'group' => $this->group ?? null,
            'fields' => $this->fields,
            'parameterTypes' => $this->parameterTypes,
        ];

        if (isset($this->parentRoute)) {
            $routeData['parentRoute'] = $this->parentRoute;
        }

        return $routeData;
    }

    /**
     * @inheritDoc
     */
    public function onRegister(): static
    {
        if (substr($this->prefix, 0, 1) != Getter::getDelimiter()) {
            $this->prefix = Getter::getDelimiter() . $this->prefix;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRouteData(): array
    {
        return $this->getData();
    }
}
