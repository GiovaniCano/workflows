<?php

use App\Http\Controllers\PagesController;
use App\Http\Controllers\WorkflowController;
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

Route::view('', 'landing-page')->name('landing-page');

Route::get('workflow/{workflow}/{slug?}', [WorkflowController::class, 'show'])->name('workflow.show')->middleware('auth');
Route::get('workflow/edit/{workflow}/{slug?}', [WorkflowController::class, 'edit'])->name('workflow.edit')->middleware('auth');
Route::resource('workflow', WorkflowController::class)->except(['show', 'edit'])->middleware('auth');

Route::get('terms-and-conditions', [PagesController::class, 'terms_and_conditions'])->name('terms');
Route::get('privacy-policy', [PagesController::class, 'privacy_policy'])->name('privacy');

Route::view('pricing', 'pricing')->name('pricing');

Route::post('set-locale', [PagesController::class, 'set_locale'])->name('set-locale');