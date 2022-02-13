<?php


namespace RTC\Http\Router\Routing;


use FastRoute\Dispatcher as FastDispatcher;
use JetBrains\PhpStorm\Pure;
use RTC\Contracts\Http\Router\CollectorInterface;
use RTC\Contracts\Http\Router\DispatchResultInterface;

class DispatchResult implements DispatchResultInterface
{
    /**
     * @var array
     */
    private array $dispatchResult;

    private CollectorInterface $collector;

    /**
     * DispatchResult constructor.
     *
     * @param string[] $dispatchResult
     * @param CollectorInterface $collector
     */
    public function __construct(array $dispatchResult, CollectorInterface $collector)
    {
        $this->dispatchResult = $dispatchResult;
        $this->collector = $collector;
    }


    /**
     * If url is found
     *
     * @return bool
     */
    public function isFound(): bool
    {
        return $this->dispatchResult[0] === FastDispatcher::FOUND;
    }

    /**
     * If url is not found
     *
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->dispatchResult[0] === FastDispatcher::NOT_FOUND;
    }

    /**
     * If url method is not allowed
     *
     * @return bool
     */
    public function isMethodNotAllowed(): bool
    {
        return $this->dispatchResult[0] === FastDispatcher::METHOD_NOT_ALLOWED;
    }

    /**
     * Get dispatched url parameters
     * @return array|null
     */
    public function getUrlParameters(): array|null
    {
        return $this->dispatchResult[2] ?? null;
    }

    /**
     * Get all collected routes
     *
     * @return CollectorInterface
     */
    public function getCollector(): CollectorInterface
    {
        return $this->collector;
    }

    /**
     * Get found url
     *
     * @return RouteData
     */
    #[Pure] public function getRoute(): RouteData
    {
        return new RouteData($this->dispatchResult[1] ?? []);
    }
}