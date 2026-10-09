<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Phpmyadmin\Http\Controllers\LinkController;

Route::post('/databases/{id}/link', [LinkController::class, 'store']);
