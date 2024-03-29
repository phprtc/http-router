<?php

namespace RTC\Http\Router\Tests;

use PHPUnit\Framework\TestCase;
use RTC\Http\Router\Route;
use RTC\Http\Router\Routing\Cache;
use RTC\Http\Router\Collector;
use RTC\Http\Router\Dispatcher;

class CacheTest extends TestCase
{
    public function testCaching(): void
    {
        Collector::create()
            ->collectFile(__DIR__ . '/routes-1.php')
            ->cache(__DIR__ . '/route-cache.php')
            ->register();

        $cache = Cache::get(__DIR__ . '/route-cache.php', false);

        self::assertFileExists(__DIR__ . '/route-cache.php');
        self::assertEquals($cache, (require __DIR__ . '/route-cache.php'));
    }

    public function testClosureCaching(): void
    {
        $message = 'hello there, quick router made it.';
        Route::get('/', function () use ($message) {
            echo $message;
        });

        $collector = Collector::create()->collect()->register();
        $result = Dispatcher::create($collector)->dispatch('get', '/');

        ob_start();
        $result->getRoute()->getHandler()();
        $echoed = ob_get_contents();
        ob_end_clean();

        self::assertSame($message, $echoed);
    }

    protected function setUp(): void
    {
        Route::restart();
    }
}
