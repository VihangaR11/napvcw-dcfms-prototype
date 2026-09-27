<?php

use App\Http\Controllers\CaseController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {

    Route::get('/cases', [CaseController::class, 'index'])
    ->name('cases.index');

    Route::get('/cases/create', [CaseController::class, 'create'])
    ->name('cases.create');

    Route::post('/cases', [CaseController::class, 'store'])
    ->name('cases.store');

    Route::get('/cases/{case}', [CaseController::class, 'show'])
    ->name('cases.show');

    Route::get('/cases/{case}/route', [CaseController::class, 'routeForm'])
    ->name('cases.route.form');

    Route::post('/cases/{case}/route', [CaseController::class, 'routeCase'])
    ->name('cases.route');


    Route::get(
    '/cases/{case}/assignments/{assignment}/assign',
    [CaseController::class, 'assignmentForm']
    )->name('cases.assignments.form');

    Route::post(
    '/cases/{case}/assignments/{assignment}/assign',
    [CaseController::class, 'assignOfficer']
    )->name('cases.assignments.assign');
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});