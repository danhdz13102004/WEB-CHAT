<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Chat\Chat;
use App\Livewire\Chat\Chatbox;
use App\Livewire\Chat\Index;
use App\Livewire\Test;
use App\Livewire\Users;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/chat',Index::class)->name('chat.index');
    Route::get('/chat/{index}',Chat::class)->name('chat');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::get('/users',Users::class)->name('chat.users');
Route::get('/test',Test::class)->name('test');


Route::post('/livewire/audio-upload', [Chatbox::class, 'audioRecorded'])->name('audio.upload');