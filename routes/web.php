<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KostController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomAddonController;
use App\Http\Controllers\PurchaseController;

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
    return redirect()->route('occupancies.index');
})->middleware(['auth'])->name('dashboard');

Route::get('/debug-menu', function () {
    return view('debug-menu');
})->middleware(['auth'])->name('debug-menu');

Route::middleware('auth')->group(function () {
    Route::resource('kost', KostController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('addons', RoomAddonController::class);
    Route::resource('consumers', App\Http\Controllers\ConsumerController::class);
    Route::post('occupancies/{occupancy}/complete', [App\Http\Controllers\RoomOccupancyController::class, 'complete'])->name('occupancies.complete');
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
    // Custom routes before resource to avoid clashing with show
    Route::get('purchases/print', [PurchaseController::class, 'print'])->name('purchases.print');
    Route::get('purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
    Route::resource('purchases', PurchaseController::class)->except(['show']);
    Route::get('role-permissions', [App\Http\Controllers\RolePermissionController::class, 'index'])->name('role-permissions.index');
    Route::get('role-permissions/{role}/edit', [App\Http\Controllers\RolePermissionController::class, 'edit'])->name('role-permissions.edit');
    Route::put('role-permissions/{role}', [App\Http\Controllers\RolePermissionController::class, 'update'])->name('role-permissions.update');
    Route::post('role-permissions/reset', [App\Http\Controllers\RolePermissionController::class, 'resetPermissions'])->name('role-permissions.reset');
    Route::get('reports/occupancy', [App\Http\Controllers\ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('reports/finance', [App\Http\Controllers\ReportController::class, 'finance'])->name('reports.finance');

    // Addon Transactions (specific routes BEFORE resource to avoid conflicts)
    Route::get('addon-transactions/consumer/{consumer}/active-room', [App\Http\Controllers\AddonTransactionController::class, 'consumerActiveRoom'])
        ->name('addon-transactions.consumer-active-room');
    Route::post('addon-transactions/{addon_transaction}/post', [App\Http\Controllers\AddonTransactionController::class, 'postToBilling'])->name('addon-transactions.post');
    Route::resource('addon-transactions', App\Http\Controllers\AddonTransactionController::class)->only(['index','create','store','show']);
    // Backward-compatible redirect for underscore path
    Route::redirect('addon_transactions', 'addon-transactions');
    
    Route::get('settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
});


require __DIR__.'/auth.php';
