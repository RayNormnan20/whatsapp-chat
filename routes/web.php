<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Livewire\ChatComponent;
use App\Http\Livewire\Admin\UserManagement;
use App\Http\Livewire\Admin\Settings;
use App\Models\Chat;
use App\Models\User;

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
    if (auth()->check()) {
        return redirect()->route('chat.index');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->resource('contacts', ContactController::class)->except(['show']);

Route::get('/chat', ChatComponent::class)
    ->middleware('auth')
    ->name('chat.index');

Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/admin/users', UserManagement::class)->name('admin.users');
    Route::get('/admin/settings', Settings::class)->name('admin.settings');
});

Route::get('prueba', function(){

    $chats = Chat::whereHas('users', function($query){
        $query->where('users.id', 3);
    })->get();

    return $chats;

});
