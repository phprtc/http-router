<?php

namespace RTC\Http\Route\Tests;

use RTC\Http\Router\Route;

if (!function_exists('RTC\Http\Route\Tests\printer')) {
    function printer(): void
    {
        //dummy
    }
}

Route::post('user/save', 'RTC\Http\Route\Tests\printer')->name('creator');
Route::patch('user/patch', 'RTC\Http\Route\Tests\printer');
Route::delete('user', 'RTC\Http\Route\Tests\printer');