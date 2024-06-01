<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/images/{path}', function ($path) {

    if (!Storage::exists('public/images/' . $path)) {
        abort(404);
    }

    $file = Storage::get('public/images/' . $path);
    $type = Storage::mimeType('public/images/' . $path);

    return response($file, 200)->header('Content-Type', $type);
})->where('path', '.+');


