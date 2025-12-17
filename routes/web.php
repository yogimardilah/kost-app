<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KostController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomAddonController;   

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

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('kost', KostController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('addons', RoomAddonController::class);
    Route::resource('consumers', App\Http\Controllers\ConsumerController::class);
    Route::resource('occupancies', App\Http\Controllers\RoomOccupancyController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('billings', App\Http\Controllers\BillingController::class);
    Route::get('billings/{billing}/download-invoice', [App\Http\Controllers\BillingController::class, 'downloadInvoice'])->name('billings.downloadInvoice');
    Route::get('billings-reminders', [App\Http\Controllers\BillingController::class, 'reminders'])->name('billings.reminders');
    Route::resource('payments', App\Http\Controllers\PaymentController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::get('reports/occupancy', [App\Http\Controllers\ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('reports/finance', [App\Http\Controllers\ReportController::class, 'finance'])->name('reports.finance');
    Route::get('settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
});


require __DIR__.'/auth.php';
