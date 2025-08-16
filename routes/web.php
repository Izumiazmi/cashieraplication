<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminTodoController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\TambahAnggotaController;
use App\Models\AdminTodo;
use App\Http\Controllers\Anggota\LoginController;
use App\Http\Controllers\Anggota\KasirController;
use App\Http\Controllers\Anggota\MenuController;
use App\Http\Controllers\Anggota\DashboardController;
use App\Http\Controllers\Anggota\ProfileController;
use App\Http\Controllers\Anggota\TransactionController;
use App\Http\Controllers\Anggota\AnalyticsController;
use App\Http\Controllers\Anggota\PembukuanController as AnggotaPembukuanController;
use App\Http\Controllers\Anggota\LaporanHarianController;


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

//admin
Route::get('/login-2864629nfndf74b^Hbuue', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/login-2864629nfndf74b^Hbuue', [AdminAuthController::class, 'login'])->name('admin.login');
Route::get('/login-2864629nfndf74b^Hbuue', function () {
    return view('login');
})->name('admin.login');

// Logout admin
Route::get('/admin/logout', function (Request $request) {
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('admin.login');
})->name('admin.logout.get');

//login anggota
Route::prefix('member')->name('anggota.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

//route admin
Route::prefix('admin/{token}')
    ->middleware(['auth:admin', 'admin.token'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        //Todo List
        Route::post('/todos', [AdminTodoController::class, 'store'])->name('admin.todos.store');
        Route::delete('/todos/{todo}', [AdminTodoController::class, 'destroy'])
            ->name('admin.todos.delete')
            ->whereNumber('todo');

        //Tambah Anggota
        Route::get('/anggota', [TambahAnggotaController::class, 'index'])->name('admin.anggota.index');
        Route::get('/anggota/create', [TambahAnggotaController::class, 'create'])->name('admin.anggota.create');
        Route::post('/anggota', [TambahAnggotaController::class, 'store'])->name('admin.anggota.store');
        Route::delete('/anggota/{anggota}', [TambahAnggotaController::class, 'destroy'])->name('admin.anggota.destroy');
        Route::get('/anggota/{anggota}', [TambahAnggotaController::class, 'show'])->name('admin.anggota.show');
        Route::post('/anggota/{anggota}', [TambahAnggotaController::class, 'update'])->name('admin.anggota.update');
    });

// route anggota
Route::prefix('member/{anggota}')->middleware('auth:anggota')->name('anggota.')->group(function () {
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');

    Route::resource('dashboard', DashboardController::class);
    Route::resource('menu', MenuController::class);
    Route::resource('transaction', TransactionController::class);

    Route::get('/history', [TransactionController::class, 'index'])->name('history.index');
    Route::get('/history/{transaction}', [TransactionController::class, 'show'])->name('history.show');

    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('/analytics/data', [AnalyticsController::class, 'getChartData'])->name('analytics.data');
    Route::get('/sales-data', [AnalyticsController::class, 'getSalesData'])->name('analytics.salesData');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/pembukuan', [AnggotaPembukuanController::class, 'index'])->name('pembukuan.index');
    Route::get('/pembukuan/{tahun}/{bulan}', [AnggotaPembukuanController::class, 'show'])->name('pembukuan.show');
    Route::get('/pembukuan/{tahun}/{bulan}/cetak', [AnggotaPembukuanController::class, 'cetakPdf'])->name('pembukuan.cetak');

    Route::get('/laporan-harian', [LaporanHarianController::class, 'index'])->name('laporan-harian.index');
    Route::get('/laporan-harian/{tanggal}', [LaporanHarianController::class, 'show'])->name('laporan-harian.show');

    Route::get('/profil', [ProfileController::class, 'show'])->name('profil.show');
    Route::post('/profil', [ProfileController::class, 'update'])->name('profil.update');

    Route::get('/history/{transaction}/json', [TransactionController::class, 'getJson'])->name('history.getJson');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/', function () {
    return view('landing_page');
});
