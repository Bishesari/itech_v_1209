<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::middleware('guest')->group(function () {
    Volt::route('registration', 'auth.my-register')->name('registration');
});


Volt::route('fields', 'field.crud')->name('fields')->middleware(['auth']);

Volt::route('standards', 'standard.index')->name('standards')->middleware(['auth']);
Volt::route('standard/create', 'standard.create')->name('create_standard')->middleware(['auth']);
Volt::route('standard/{standard}/edit', 'standard.edit')->name('edit_standard')->middleware(['auth', 'signed']);
Volt::route('standard/{standard}/chapters', 'standard.chapter.crud')->name('chapters')->middleware(['auth', 'signed']);


Volt::route('question/{sid}/{cid}/index', 'question.index')->name('questions')->middleware(['auth', 'signed']);
Volt::route('question/{sid}/{cid}/create', 'question.create')->name('create_question')->middleware(['auth', 'signed']);

