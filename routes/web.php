<?php
use App\Http\Controllers\RecordController;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', [RecordController::class, 'dashboard'])->name('dashboard');

Route::get('/basic_elements', [RecordController::class, 'basic'])->name('basic_elements');
Route::post('/basic_elements', [RecordController::class, 'record'])->name('basic_elements');;
Route::get('/download-zip', [RecordController::class, 'downloadZip'])->name('download.zip');
Route::get('/check-gd', function () {
    if (function_exists('gd_info')) {
        return 'GD extension is enabled.';
    } else {
        return 'GD extension is not enabled.';
    }
});