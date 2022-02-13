<?php


namespace RTC\Http\Router;

use FastRoute\Dispatcher as FastRouteDispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use JetBrains\PhpStorm\Pure;
use RTC\Contracts\Http\Router\CollectorInterface;
use RTC\Http\Router\Routing\DispatchResult;
use RTC\Http\Router\Routing\Getter;

class Dispatcher
{
    private string $dispatcher;


    public function __construct(private CollectorInterface $collector)
    {
    }

    /**
     * Collect routes defined above or in included file
     *
     * @param array $routesInfo
     * @return static
     */
    public static function collectRoutes(array $routesInfo = []): static
    {
        return self::create(Collector::create()->collect($routesInfo));
    }

    /**
     * Creates dispatcher instance
     *
     * @param CollectorInterface $collector
     * @return static
     */
    #[Pure] public static function create(CollectorInterface $collector): static
    {
        return new static($collector);
    }

    /**
     * Collect routes defined in a file
     *
     * @param string $filePath
     * @param array $routesInfo
     * @return static
     */
    public static function collectRoutesFile(string $filePath, array $routesInfo = []): static
    {
        return self::create(Collector::create()->collectFile($filePath, $routesInfo));
    }

    /**
     * Dispatch url routing
     * @param string $method Route method - It will be converted to uppercase
     * @param string $path Route url path - All data passed to url parameter after "?" will be discarded
     * @return DispatchResult
     */
    public function dispatch(string $method, string $path): DispatchResult
    {
        $lengthPath = strlen($path) - 1;

        //Make url convertible
        if (false !== $pos = strpos($path, '?')) {
            $path = substr($path, 0, $pos);
        }

        //invalid & in url at ? position
        if (false !== $pos = strpos($path, '&')) {
            $path = substr($path, 0, $pos);
        }

        $path = str_replace('//', '/', $path);
        $path = rawurldecode($path);

        //Remove trailing forward slash
        if (($lengthPath > 0) && substr($path, $lengthPath, 1) == Getter::getDelimiter()) {
            $path = substr($path, 0, $lengthPath);
        }

        if (substr($path, 0, 1) != Getter::getDelimiter()) {
            $path = Getter::getDelimiter() . $path;
        }

        $urlData = $this->createDispatcher()
            ->dispatch(strtoupper($method), $path);

        return new DispatchResult($urlData, $this->collector);
    }

    /**
     * @return FastRouteDispatcher
     */
    private function createDispatcher(): FastRouteDispatcher
    {
        if (!isset($this->dispatcher)) {
            $this->dispatcher = GroupCountBased::class;
        }

        //Register collector if it is not registered
        if (!$this->collector->isRegistered()) {
            $this->collector->register();
        }

        $dispatcher = $this->dispatcher;
        $routeData = $this->collector->getFastRouteData();

        /**@phpstan-ignore-next-line**/
        return (new $dispatcher($routeData));
    }

    /**
     * Set your own dispatcher
     * @param string $dispatcher A class namespace implementing \FastRoute\Dispatcher
     * @return static
     */
    public function setDispatcher(string $dispatcher): static
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }
}