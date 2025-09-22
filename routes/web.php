<?php

use Illuminate\Support\Facades\Route;
use App\Models\Produk;
use App\Http\Controllers\{
    POController,
    ProfileController,
    POExportController,
    KendaraanController,
    ProdukController,
    CustomerController,
    PengirimController, // Added PengirimController import
    SuratJalanController,
    InvoiceController,
    JatuhTempoController,
    EmployeeController,
    SalaryController,
    DashboardController,
    ExpenseController,
    FinanceController,
    UserController,
    BarangMasukController,
    BarangKeluarController,
    SisaDataPOController,
    ExcelTemplateController
};

/*
|--------------------------------------------------------------------------
| Redirect Root ke Login
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | API: Get Harga Produk by ID
    |--------------------------------------------------------------------------
    */
    Route::get('/get-harga/{id}', function ($id) {
        $produk = Produk::find($id);
        return response()->json([
            'harga' => $produk?->harga ?? 0,
        ]);
    })->name('produk.get-harga');

    /*
    |--------------------------------------------------------------------------
    | Dashboard & Halaman Statis
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::view('/invoice', 'dashboard.invoice')->name('invoice');
    // Route halaman Tanda Terima dihapus: fitur dipusatkan pada halaman Surat Jalan
    // Route static berikut dinonaktifkan agar tidak menimpa controller JatuhTempoController@index
    // Route::view('/jatuh-tempo', 'dashboard.jatuh-tempo')->name('jatuh-tempo');
    // Finance dashboard (Pendapatan & Pengeluaran)
    Route::get('/finance', [FinanceController::class, 'income'])->name('finance.index'); // alias utama diarahkan ke Pendapatan
    Route::get('/finance/pendapatan', [FinanceController::class, 'income'])->name('finance.income');
    Route::get('/finance/pengeluaran', [FinanceController::class, 'expense'])->name('finance.expense');
    // Finance detail (JSON)
    Route::get('/finance/pendapatan/detail', [FinanceController::class, 'incomeDetail'])->name('finance.income.detail');
    Route::get('/finance/pengeluaran/detail', [FinanceController::class, 'expenseDetail'])->name('finance.expense.detail');
    Route::post('/finance/pengeluaran/store', [FinanceController::class, 'storeExpense'])->name('finance.expense.store');

    /*
    |--------------------------------------------------------------------------
    | Settings Page
    |--------------------------------------------------------------------------
    */
    Route::view('/settings', 'settings.index')->name('settings');

    /*
    |--------------------------------------------------------------------------
    | Users: Index (admin only view), Create & Store
    |--------------------------------------------------------------------------
    */
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::resource('users', UserController::class)->only(['create','store']);

    /*
    |--------------------------------------------------------------------------
    | Kendaraan (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::resource('kendaraan', KendaraanController::class);

    /*
    |--------------------------------------------------------------------------
    | Produk (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::resource('produk', ProdukController::class);

    /*
    |--------------------------------------------------------------------------
    | Barang Masuk & Barang Keluar
    |--------------------------------------------------------------------------
    */
    Route::prefix('barang')->name('barang.')->group(function () {
        Route::resource('masuk', BarangMasukController::class)->names('masuk');
        Route::resource('keluar', BarangKeluarController::class)->names('keluar');
    });

    /*
    |--------------------------------------------------------------------------
    | Customer (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::resource('customer', CustomerController::class);

    /*
    |--------------------------------------------------------------------------
    | Pengirim (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::resource('pengirim', PengirimController::class);

    /*
    |--------------------------------------------------------------------------
    | Employee Management (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::patch('/{employee}/status', [EmployeeController::class, 'updateStatus'])->name('update-status');
    });

    /*
    |--------------------------------------------------------------------------
    | Salary Management (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::prefix('salary')->name('salary.')->group(function () {
        Route::get('/', [SalaryController::class, 'index'])->name('index');
        Route::get('/create', [SalaryController::class, 'create'])->name('create');
        Route::post('/', [SalaryController::class, 'store'])->name('store');
        Route::get('/{salary}/edit', [SalaryController::class, 'edit'])->name('edit');
        Route::put('/{salary}', [SalaryController::class, 'update'])->name('update');
        Route::delete('/{salary}', [SalaryController::class, 'destroy'])->name('destroy');
        Route::post('/generate-payroll', [SalaryController::class, 'generatePayroll'])->name('generate-payroll');
        Route::patch('/{salary}/payment', [SalaryController::class, 'markAsPaid'])->name('mark-paid');
    });

    /*
    |--------------------------------------------------------------------------
    | Excel Template Generator
    |--------------------------------------------------------------------------
    */
    Route::get('/excel/generate', [ExcelTemplateController::class, 'generateFromExcel'])->name('excel.generate');

    /*
    |--------------------------------------------------------------------------
    | Invoice Management (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::patch('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
        // Hapus semua data Invoice (+ Jatuh Tempo terkait)
        Route::delete('/all', [InvoiceController::class, 'destroyAll'])->name('destroy-all');
    });

    // Seluruh route CRUD Tanda Terima dinonaktifkan (dipusatkan ke Surat Jalan)
    
    /*
    |--------------------------------------------------------------------------
    | Jatuh Tempo (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::prefix('jatuh-tempo')->name('jatuh-tempo.')->group(function () {
        Route::get('/', [JatuhTempoController::class, 'index'])->name('index');
        Route::get('/create', [JatuhTempoController::class, 'create'])->name('create');
        Route::post('/', [JatuhTempoController::class, 'store'])->name('store');
        Route::get('/{jatuhTempo}/edit', [JatuhTempoController::class, 'edit'])->name('edit');
        Route::put('/{jatuhTempo}', [JatuhTempoController::class, 'update'])->name('update');
        Route::delete('/{jatuhTempo}', [JatuhTempoController::class, 'destroy'])->name('destroy');
        Route::patch('/{jatuhTempo}/payment', [JatuhTempoController::class, 'markAsPaid'])->name('mark-paid');
        Route::get('/{id}/send-reminder', [JatuhTempoController::class, 'sendReminder'])->name('send-reminder');
        Route::post('/{id}/update-status', [JatuhTempoController::class, 'updateStatus'])->name('update-status');
        Route::put('/{id}/update-deadline', [JatuhTempoController::class, 'updateDeadline'])->name('update-deadline');
        // Route hapus semua Jatuh Tempo dihapus sesuai permintaan (demi keamanan data)
    });

    /*
    |--------------------------------------------------------------------------
    | Surat Jalan (CRUD + Export + Invoice)
    |--------------------------------------------------------------------------
    */
    // Ubah path URL dari /suratjalan menjadi /data-po, penamaan route tetap 'suratjalan.*'
    Route::prefix('data-po')->name('suratjalan.')->group(function () {
        Route::get('/', [SuratJalanController::class, 'index'])->name('index');
        Route::post('/', [SuratJalanController::class, 'store'])->name('store');
        Route::get('/{suratJalan}/edit-form', [SuratJalanController::class, 'editForm'])->name('edit-form');
        Route::get('/{suratJalan}/edit', [SuratJalanController::class, 'edit'])->name('edit');
        Route::put('/{suratJalan}', [SuratJalanController::class, 'update'])->name('update');
        Route::delete('/{suratJalan}', [SuratJalanController::class, 'destroy'])->name('destroy');
        // Arahkan export ke controller yang menggunakan template Excel
        Route::post('/export', [POExportController::class, 'exportToExcel'])->name('export');
        
        Route::post('/invoice-data', [SuratJalanController::class, 'getInvoiceData'])->name('invoice.data');
        Route::post('/invoice-pdf', [SuratJalanController::class, 'generateInvoicePDF'])->name('invoice.pdf');
        Route::post('/{id}/update-status', [SuratJalanController::class, 'updateStatus'])->name('update-status');
        // Hapus semua data Surat Jalan (+ Jatuh Tempo terkait)
        Route::delete('/all', [SuratJalanController::class, 'destroyAll'])->name('destroy-all');
    });

    /*
    |--------------------------------------------------------------------------
    | Purchase Order (PO + Export)
    |--------------------------------------------------------------------------
    */
    Route::prefix('po')->name('po.')->group(function () {
        Route::get('/', [POController::class, 'index'])->name('index');
        Route::get('/create', [POController::class, 'create'])->name('create');
        Route::post('/', [POController::class, 'store'])->name('store');
        Route::get('/{po}/edit', [POController::class, 'edit'])->name('edit');
        Route::put('/{po}', [POController::class, 'update'])->name('update');
        Route::delete('/{po}', [POController::class, 'destroy'])->name('destroy');
        
        // Export PO di-nonaktifkan. Gunakan export di menu Surat Jalan.
        // Route::get('/export', [POExportController::class, 'exportToExcel'])->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | Invoice Routes (Independent from PO)
    |--------------------------------------------------------------------------
    */
    Route::post('/po/{po}/toggle-status', [POController::class, 'toggleStatus'])->name('po.toggle-status');
    Route::post('/invoice/quick-create', [POController::class, 'invoiceQuickCreate'])->name('invoice.quick-create');
    Route::post('/invoice/set-next-number', [POController::class, 'setNextInvoiceNumber'])->name('invoice.set-next-number');
    Route::get('/invoice', [POController::class, 'invoiceIndex'])->name('invoice.index');

    /*
    |--------------------------------------------------------------------------
    | Sisa Data Purchase Order
    |--------------------------------------------------------------------------
    */
    Route::prefix('sisa-data-po')->name('sisa-data-po.')->group(function () {
        Route::get('/', [SisaDataPOController::class, 'index'])->name('index');
        Route::get('/export', [SisaDataPOController::class, 'export'])->name('export');
        Route::get('/detail', [SisaDataPOController::class, 'getDetail'])->name('detail');
        Route::get('/{id}/edit', [SisaDataPOController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SisaDataPOController::class, 'update'])->name('update');
        Route::delete('/{id}', [SisaDataPOController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| API Routes for Notifications
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/jatuh-tempo/notifications', [App\Http\Controllers\Api\NotificationController::class, 'jatuhTempoNotifications']);
});
