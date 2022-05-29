<?php

use Illuminate\Support\Facades\Route;

Route::post('/v1/winners', 'Api\V1\WinnerController@store')->name('api.v1.winners.store');
