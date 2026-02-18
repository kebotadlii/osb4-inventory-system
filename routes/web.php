<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    ExcelController,
    CategoryController,
    ItemController,
    ItemTransactionController,
    HistoryController,
    ReportController,
    ProfileController,
    ExpenseController,
    ExpenseCategoryController
};

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('dashboard'));

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| AUTHENTICATED AREA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | CATEGORIES + ITEMS PER CATEGORY
    |--------------------------------------------------------------------------
    */
    Route::prefix('categories')->group(function () {

        Route::get('/', [CategoryController::class, 'index'])
            ->name('categories.index');

        Route::get('/create', [CategoryController::class, 'create'])
            ->name('categories.create');

        Route::post('/', [CategoryController::class, 'store'])
            ->name('categories.store');

        /*
        |--------------------------------------------------------------------------
        | ITEMS BY CATEGORY
        |--------------------------------------------------------------------------
        */
        Route::prefix('{category}/items')->group(function () {

            Route::get('/', [ItemController::class, 'byCategory'])
                ->name('categories.items');

            // FORM IMPORT
            Route::get('/import', [ItemController::class, 'importForm'])
                ->name('categories.items.import.form');

            // PROCESS IMPORT
            Route::post('/import', [ItemController::class, 'import'])
                ->name('categories.items.import.process');

            // DOWNLOAD TEMPLATE
            Route::get('/import/template', [ItemController::class, 'downloadTemplate'])
                ->name('categories.items.import.template');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ITEMS GLOBAL
    |--------------------------------------------------------------------------
    */
    Route::prefix('items')->group(function () {

        Route::get('/', [ItemController::class, 'index'])
            ->name('items.all');

        Route::get('/create', [ItemController::class, 'create'])
            ->name('items.create');

        Route::post('/', [ItemController::class, 'store'])
            ->name('items.store');

        Route::put('/{id}', [ItemController::class, 'updateItem'])
            ->name('items.update');

        Route::delete('/{id}', [ItemController::class, 'deleteItem'])
            ->name('items.delete');

        Route::get('/{id}/history', [ItemController::class, 'history'])
            ->name('items.history');
    });

    /*
    |--------------------------------------------------------------------------
    | TRANSACTIONS
    |--------------------------------------------------------------------------
    */
    Route::prefix('transactions')->group(function () {

        // STOCK IN
        Route::get('/in', [ItemTransactionController::class, 'createIn'])
            ->name('transactions.in.form');

        Route::post('/in', [ItemTransactionController::class, 'storeIn'])
            ->name('transactions.in.store');

        // STOCK OUT
        Route::get('/out', [ItemTransactionController::class, 'createOut'])
            ->name('transactions.out.form');

        Route::post('/out', [ItemTransactionController::class, 'storeOut'])
            ->name('transactions.out.store');

        // IMPORT STOCK OUT
        Route::post('/out/import', [ExcelController::class, 'importItemOut'])
            ->name('transactions.out.import');

        // DOWNLOAD TEMPLATE STOCK OUT
        Route::get('/out/import/template', [ExcelController::class, 'downloadItemOutTemplate'])
            ->name('transactions.out.import.template');
    });

    /*
    |--------------------------------------------------------------------------
    | HISTORY
    |--------------------------------------------------------------------------
    */
    Route::get('/history', [HistoryController::class, 'index'])
        ->name('history.index');

    Route::get('/history/export', [HistoryController::class, 'export'])
        ->name('history.export');

    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->group(function () {

        Route::get('/', [ReportController::class, 'stock'])
            ->name('reports.index');

        Route::get('/stock', [ReportController::class, 'stock'])
            ->name('reports.stock');

        Route::get('/stock/export', [ReportController::class, 'exportStock'])
            ->name('reports.stock.export');

        Route::get('/expenses', [ReportController::class, 'expenses'])
            ->name('reports.expenses');

        Route::get('/expenses/export', [ReportController::class, 'exportExpenses'])
            ->name('reports.expenses.export');
    });

    /*
    |--------------------------------------------------------------------------
    | EXPENSE CATEGORIES
    |--------------------------------------------------------------------------
    */
    Route::prefix('expense-categories')->group(function () {

        Route::get('/', [ExpenseCategoryController::class, 'index'])
            ->name('expense.categories.index');

        Route::get('/create', [ExpenseCategoryController::class, 'create'])
            ->name('expense.categories.create');

        Route::post('/', [ExpenseCategoryController::class, 'store'])
            ->name('expense.categories.store');

        Route::delete('/{id}', [ExpenseCategoryController::class, 'destroy'])
            ->name('expense.categories.delete');

        Route::get('/{id}/import', [ExpenseCategoryController::class, 'showImportForm'])
            ->name('expense.categories.import.form');

        Route::post('/{id}/import', [ExpenseCategoryController::class, 'import'])
            ->name('expense.categories.import.process');

        Route::get('/{id}/import/template', [ExpenseCategoryController::class, 'downloadTemplate'])
            ->name('expense.categories.import.template');
    });

    /*
    |--------------------------------------------------------------------------
    | EXPENSES
    |--------------------------------------------------------------------------
    */
    Route::prefix('expenses')->group(function () {

        Route::get('/', [ExpenseController::class, 'index'])
            ->name('expenses.index');

        Route::get('/create', [ExpenseController::class, 'create'])
            ->name('expenses.create');

        Route::post('/', [ExpenseController::class, 'store'])
            ->name('expenses.store');

        Route::get('/{id}/edit', [ExpenseController::class, 'edit'])
            ->name('expenses.edit');

        Route::put('/{id}', [ExpenseController::class, 'update'])
            ->name('expenses.update');

        Route::delete('/{id}', [ExpenseController::class, 'destroy'])
            ->name('expenses.destroy');
    });
});
