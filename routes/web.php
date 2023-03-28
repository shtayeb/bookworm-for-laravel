<?php

use SHTayeb\Bookworm\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::resource('roles', TestController::class);
