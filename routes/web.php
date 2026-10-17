<?php

use App\Http\Controllers\GeneralController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

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

Route::get("/create", function () {
    User::create([
        "name" => "tauseed",
        "email" => "tauseed@gmail.com",
        "password" => bcrypt("password"),
    ]);
});

Route::get('/', [GeneralController::class, "index"])->name("index");
Route::get('/about', [GeneralController::class, "about"])->name("about");
Route::get('/projects', [GeneralController::class, "projects"])->name("projects");
Route::get('/photos', [GeneralController::class, "photos"])->name("photos");

Route::get('/storage/{path}', function (string $path) {
    $cleanPath = str_replace(['..', "\0"], '', $path);
    $fullPath = storage_path('app/public/' . $cleanPath);

    if (file_exists($fullPath) && is_file($fullPath)) {
        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    abort(404);
})->where('path', '.*')->name('storage.fallback');
