<?php


namespace RTC\Http\Router\Tests;


use JetBrains\PhpStorm\Pure;
use RTC\Http\Router\Routing\TheRoute;

class TheRouteFactory extends TheRoute
{
    private bool $enableOnRegisterEvent;

    #[Pure] public function __construct(bool $enableOnRegisterEvent = false)
    {
        parent::__construct();
        $this->enableOnRegisterEvent = $enableOnRegisterEvent;
    }

    public function onRegister(): static
    {
        if ($this->enableOnRegisterEvent) {
            return parent::onRegister();
        }

        return $this;
    }
}