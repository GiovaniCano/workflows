<?php

use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserController;
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

// Route::view('', 'landing-page')->name('landing-page');
Route::redirect('', 'workflow', 302)->name('landing-page');

Route::resource('workflow', WorkflowController::class)->except(['show', 'edit'])->middleware('auth');
Route::get('workflow/edit/{workflow}/{slug?}', [WorkflowController::class, 'edit'])->name('workflow.edit')->middleware('auth');
Route::get('workflow/{workflow}/{slug?}', [WorkflowController::class, 'show'])->name('workflow.show')->middleware('auth');

Route::get('terms-and-conditions', [PagesController::class, 'terms_and_conditions'])->name('terms');
Route::get('privacy-policy', [PagesController::class, 'privacy_policy'])->name('privacy');

// Route::view('pricing', 'pricing')->name('pricing');

Route::post('set-locale', [PagesController::class, 'set_locale'])->name('set-locale');

Route::prefix('user')->controller(UserController::class)->middleware('auth')->group(function() {
    Route::get('profile', 'profile')->name('user.profile');
    // Route::get('payment', 'payment')->name('user.payment');
});