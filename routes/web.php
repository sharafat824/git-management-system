<?php

use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyGitHubWebhook;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\GitHubWebhookController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';


Route::middleware('auth')->group(function ()  {
Route::get('/repo', [RepositoryController::class, 'index'])->name('repositories.index');
Route::get('/repositories/{repository}/branches', [RepositoryController::class, 'branches'])->name('repositories.branches');
});


