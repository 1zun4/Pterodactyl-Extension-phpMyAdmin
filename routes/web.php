<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Phpmyadmin\Http\Controllers\VerifyController;

Route::get('/verify/{token}', VerifyController::class)->middleware('throttle:30,1');
