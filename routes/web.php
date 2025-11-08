<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () { return view('welcome');})->name('home');
Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

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

    Volt::route('select_role', 'auth.select-role')->name('select_role');
});

Route::middleware('guest')->group(function () {
    Volt::route('registration', 'auth.my-register')->name('registration');
    Volt::route('forgotten-password', 'auth.my-forgot-password')
        ->name('forgotten.password');
});



Volt::route('fields', 'field.crud')->name('fields')->middleware(['auth']);

Volt::route('standards', 'standard.index')->name('standards')->middleware(['auth']);
Volt::route('standard/create', 'standard.create')->name('create_standard')->middleware(['auth']);
Volt::route('standard/{standard}/edit', 'standard.edit')->name('edit_standard')->middleware(['auth', 'signed']);
Volt::route('standard/{standard}/chapters', 'standard.chapter.crud')->name('chapters')->middleware(['auth', 'signed']);

Volt::route('question/{sid}/{cid}/index', 'question.index')->name('questions')->middleware(['auth', 'signed']);
Volt::route('question/{sid}/{cid}/create', 'question.create')->name('create_question')->middleware(['auth', 'signed']);

Volt::route('users', 'user.index')->name('users')->middleware('auth');
Volt::route('user/{user}/show', 'user.show')->name('show_user')->middleware(['auth', 'signed']);

Volt::route('institutes', 'institute.index')->name('institutes')->middleware('auth');
Volt::route('institute/{institute}/founders', 'institute.founder.index')->name('institute_founders')->middleware(['auth', 'signed']);

Volt::route('roles', 'role.index')->name('roles')->middleware('auth');
Volt::route('role/{role}/show', 'role.show')->name('show_role')->middleware(['auth', 'signed']);
