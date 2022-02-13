<?php


namespace RTC\Http\Router;


use JetBrains\PhpStorm\Pure;
use RTC\Http\Router\Routing\TheRoute;

class Crud
{
    protected string $uri;
    protected string $controller;

    protected string $parameterName = 'id';
    protected string $parameterRegExp = '';

    protected array $disabledRoutes = [];


    #[Pure] public static function create(
        string $uri,
        string $controller
    ): static
    {
        return new static($uri, $controller);
    }

    public function __construct(
        string $uri,
        string $controller
    )
    {
        $this->uri = $uri;
        $this->controller = $controller;
    }

    /**
     * Set id parameter name
     *
     * @param string $name parameter name
     * @param string|null $regExp regular expression
     * @return static
     */
    public function parameter(string $name, ?string $regExp = null): static
    {
        $this->parameterName = $name;

        if ($regExp) {
            $this->parameterRegExp = $regExp;
        }

        return $this;
    }

    /**
     * Mark parameter as of numeric type
     *
     * @param string $name parameter name
     * @return static
     */
    public function numericParameter(string $name = 'id'): static
    {
        return $this->parameter($name, ':[0-9]+');
    }

    /**
     * Mark parameter as of alphanumeric type
     *
     * @param string $name
     * @return static
     */
    public function alphabeticParameter(string $name = 'id'): static
    {
        return $this->parameter($name, ':[a-zA-Z]+');
    }

    /**
     * Mark parameter as of alphanumeric type
     *
     * @param string $name
     * @return static
     */
    public function alphaNumericParameter(string $name = 'id'): static
    {
        return $this->parameter($name, ':[a-zA-Z]+');
    }

    /**
     * Perform the routes creation
     */
    public function go(): void
    {
        $idParam = '{' . "{$this->parameterName}{$this->parameterRegExp}" . '}';

        //  GET /whatever
        if (!in_array('index', $this->disabledRoutes)) {
            $route = new TheRoute();
            $route->get($this->uri, [$this->controller, 'index'])->name('index');
            Route::push($route);
        }

        //  POST /whatever
        if (!in_array('store', $this->disabledRoutes)) {
            $route = new TheRoute();
            $route->post($this->uri, [$this->controller, 'store'])->name('store');
            Route::push($route);
        }

        //  DELETE /whatever
        if (!in_array('destroy_all', $this->disabledRoutes)) {
            $route = new TheRoute();
            $route->delete($this->uri, [$this->controller, 'destroyAll'])->name('destroy_all');
            Route::push($route);
        }

        //  GET /whatever/{$id}
        if (!in_array('show', $this->disabledRoutes)) {
            $route = new TheRoute();
            $route->get("$this->uri/$idParam", [$this->controller, 'show'])->name('show');
            Route::push($route);
        }

        //  PATCH /whatever/{$id}
        if (!in_array('update', $this->disabledRoutes)) {
            $route = new TheRoute();
            $route->put("$this->uri/$idParam", [$this->controller, 'update'])->name('update');
            Route::push($route);
        }

        //  DELETE /whatever/{$id}
        if (!in_array('destroy', $this->disabledRoutes)) {
            $route = new TheRoute();
            $route->delete("$this->uri/$idParam", [$this->controller, 'destroy'])->name('destroy');
            Route::push($route);
        }
    }

    /**
     * This will prevent the get all route from generating
     *
     * @return static
     */
    public function disableIndexRoute(): static
    {
        $this->disabledRoutes[] = 'index';
        return $this;
    }

    /**
     * This will prevent the "create" route from generating
     *
     * @return static
     */
    public function disableStoreRoute(): static
    {
        $this->disabledRoutes[] = 'store';
        return $this;
    }

    /**
     * This will prevent the "destroy all" route from generating
     *
     * @return static
     */
    public function disableDestroyAllRoute(): static
    {
        $this->disabledRoutes[] = 'destroy_all';
        return $this;
    }

    /**
     * This will prevent the get one route from generating
     *
     * @return static
     */
    public function disableShowRoute(): static
    {
        $this->disabledRoutes[] = 'show';
        return $this;
    }

    /**
     * This will prevent the update route from generating
     *
     * @return static
     */
    public function disableUpdateRoute(): static
    {
        $this->disabledRoutes[] = 'update';
        return $this;
    }

    /**
     * This will prevent the "delete" route from generating
     *
     * @return static
     */
    public function disableDestroyRoute(): static
    {
        $this->disabledRoutes[] = 'destroy';
        return $this;
    }
}