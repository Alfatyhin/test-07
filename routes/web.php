<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\ParserController;
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


Route::get('/', [Controller::class, 'index'])
    ->name('index');

Route::get('/parse-names',function(){
    dispatch(new App\Jobs\ParseNames());
    dd('done');
})->name('parse_names');

Route::get('/parse-movies', [ParserController::class, 'parseMovies'])
    ->name('parse_movies');

Route::get('/test-parse-movies', [ParserController::class, 'testParseMovies'])
    ->name('test_parse_movies');

//
//Route::get('/parse-movies', function(){
//    dispatch(new App\Jobs\ParseMovies());
//    dd('done');
//});
