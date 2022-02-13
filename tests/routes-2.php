<?php

namespace RTC\Http\Route\Tests;

use RTC\Http\Router\Route;

Route::post('admin/save', 'RTC\Http\Route\Tests\printer')->name('creator');
Route::patch('admin/patch', 'RTC\Http\Route\Tests\printer');
Route::delete('admin', 'RTC\Http\Route\Tests\printer');