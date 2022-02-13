<?php

namespace RTC\Http\Route\Tests;

use PHPUnit\Framework\TestCase;
use RTC\Http\Router\Route;
use RTC\Http\Router\Routing\Getter;

class DelimiterTest extends TestCase
{
    public function testPrefix(): void
    {
        Route::prefix('planets')->group(function () {
            Route::get('earth', fn() => time());
        });

        $this->assertEquals('/', Getter::getDelimiter());

        $routeData = Getter::create()
            ->prefixDelimiter('.')
            ->get(Route::getRoutes());

        $this->assertEquals('.', Getter::getDelimiter());
        $this->assertEquals('.planets.earth', $routeData[0]['prefix']);
    }

    protected function setUp(): void
    {
        Route::restart();
    }
}
