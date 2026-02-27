<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\AdminController;

Route::get('/', fn() => view('welcome'));

Route::middleware(['auth', 'banned'])->group(function () {

    // Dashboard 
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Colocations
    Route::get('/colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('/colocations', [ColocationController::class, 'store'])->name('colocations.store');
    Route::get('/colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show');
    Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');

    // Owner actions
    Route::middleware('owner')->group(function () {
        Route::patch('/colocations/{colocation}', [ColocationController::class, 'update'])->name('colocations.update');
        Route::post('/colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');

        Route::post('/colocations/{colocation}/invitations', [InvitationController::class, 'store'])->name('invitations.store');

        Route::post('/colocations/{colocation}/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::patch('/colocations/{colocation}/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/colocations/{colocation}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::post('/colocations/{colocation}/members/{user}/remove', [ColocationController::class, 'removeMember'])
            ->name('colocations.members.remove');
    });

    // Invitations 
    Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{token}/refuse', [InvitationController::class, 'refuse'])->name('invitations.refuse');

    // Dépenses
    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Settlements 
    Route::post('/colocations/{colocation}/settlements/mark-paid', [SettlementController::class, 'markPaid'])->name('settlements.markPaid');

    // Admin global
    Route::middleware('admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::post('/admin/users/{user}/ban', [AdminController::class, 'ban'])->name('admin.users.ban');
        Route::post('/admin/users/{user}/unban', [AdminController::class, 'unban'])->name('admin.users.unban');
    });
});

require __DIR__.'/auth.php';