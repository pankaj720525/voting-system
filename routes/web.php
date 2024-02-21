<?php

use App\Http\Controllers\PollController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/get-polls', [App\Http\Controllers\HomeController::class, 'getPolls'])->name('get.polls');
Route::post('/submit-poll', [App\Http\Controllers\HomeController::class, 'submitPoll'])->name('submit.poll');

Route::match(['GET','POST'],'/polls', [PollController::class, 'index'])->name('polls');
Route::get('/polls/create', [PollController::class, 'create'])->name('poll.create');
Route::post('/polls/store', [PollController::class, 'store'])->name('poll.store');
Route::get('/polls/{id}/edit', [PollController::class, 'edit'])->name('poll.edit');
Route::get('/polls/{id}/show', [PollController::class, 'show'])->name('poll.show');
Route::post('/polls/{id}/update', [PollController::class, 'update'])->name('poll.update');
Route::post('/polls/exists', [PollController::class, 'exists'])->name('poll.exists');
Route::delete('/polls/delete', [PollController::class, 'destroy'])->name('poll.delete');
