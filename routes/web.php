<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VideoUploadController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('VideoUpload');
});

Route::post('/upload', [VideoUploadController::class, 'upload']);
