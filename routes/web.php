<?php

use App\Http\Controllers\GateController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', InvitationController::class)->name('invitation');

Route::post('/unlock', [GateController::class, 'unlock'])
    ->middleware('throttle:10,1')
    ->name('gate.unlock');

Route::delete('/unlock', [GateController::class, 'lock'])->name('gate.lock');
