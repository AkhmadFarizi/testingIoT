<?php

use App\Http\Controllers\DataSensorController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/chart', [DataSensorController::class, 'index'])->name('chart.index');
Route::get('/chart/data', [DataSensorController::class, 'getData'])->name('chart.data');
Route::get('/sensor/data', [DataSensorController::class, 'getSensorData'])->name('sensor.data');

