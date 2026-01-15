<?php

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PermissionsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes de gestion des utilisateurs, rôles et permissions
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // ========================================
    // UTILISATEURS
    // ========================================
    Route::prefix('users')->name('users.')->group(function () {
        // Routes principales CRUD
        Route::get('/', [UsersController::class, 'index'])->name('index')->middleware('can:users.read');
        Route::get('/create', [UsersController::class, 'create'])->name('create')->middleware('can:users.create');
        Route::post('/', [UsersController::class, 'store'])->name('store')->middleware('can:users.create');
        Route::get('/{user}', [UsersController::class, 'show'])->name('show')->middleware('can:users.view-details');
        Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('edit')->middleware('can:users.update');
        Route::put('/{user}', [UsersController::class, 'update'])->name('update')->middleware('can:users.update');
        Route::delete('/{user}', [UsersController::class, 'destroy'])->name('destroy')->middleware('can:users.delete');

        // Routes spéciales
        Route::patch('/{user}/toggle-status', [UsersController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:users.toggle-status');
        Route::patch('/{user}/reset-password', [UsersController::class, 'resetPassword'])->name('reset-password')->middleware('can:users.update');

        // Corbeille
        Route::get('/trash/list', [UsersController::class, 'trash'])->name('trash')->middleware('can:users.view-trash');
        Route::patch('/trash/{id}/restore', [UsersController::class, 'restore'])->name('restore')->middleware('can:users.restore');
        Route::delete('/trash/{id}/force-delete', [UsersController::class, 'forceDestroy'])->name('force-destroy')->middleware('can:users.force-delete');
    });

    // ========================================
    // RÔLES
    // ========================================
    Route::prefix('roles')->name('roles.')->group(function () {
        // Routes principales CRUD
        Route::get('/', [RolesController::class, 'index'])->name('index')->middleware('can:roles.read');
        Route::get('/create', [RolesController::class, 'create'])->name('create')->middleware('can:roles.create');
        Route::post('/', [RolesController::class, 'store'])->name('store')->middleware('can:roles.create');
        Route::get('/{role}', [RolesController::class, 'show'])->name('show')->middleware('can:roles.view-details');
        Route::get('/{role}/edit', [RolesController::class, 'edit'])->name('edit')->middleware('can:roles.update');
        Route::put('/{role}', [RolesController::class, 'update'])->name('update')->middleware('can:roles.update');
        Route::delete('/{role}', [RolesController::class, 'destroy'])->name('destroy')->middleware('can:roles.delete');

        // Gestion des permissions
        Route::get('/{role}/permissions', [RolesController::class, 'permissions'])->name('permissions')->middleware('can:roles.manage');
        Route::post('/{role}/permissions', [RolesController::class, 'updatePermissions'])->name('permissions.update')->middleware('can:roles.manage');

        // Duplication
        Route::post('/{role}/duplicate', [RolesController::class, 'duplicate'])->name('duplicate')->middleware('can:roles.duplicate');
    });

    // ========================================
    // PERMISSIONS
    // ========================================
    Route::prefix('permissions')->name('permissions.')->group(function () {
        // Routes principales CRUD
        Route::get('/', [PermissionsController::class, 'index'])->name('index')->middleware('can:role_permissions.read');
        Route::post('/', [PermissionsController::class, 'store'])->name('store')->middleware('can:role_permissions.create');
        Route::get('/{permission}', [PermissionsController::class, 'show'])->name('show')->middleware('can:role_permissions.view-details');
        Route::put('/{permission}', [PermissionsController::class, 'update'])->name('update')->middleware('can:role_permissions.update');
        Route::delete('/{permission}', [PermissionsController::class, 'destroy'])->name('destroy')->middleware('can:role_permissions.delete');

        // Actions spécifiques
        Route::patch('/{permission}/activate', [PermissionsController::class, 'activate'])->name('activate')->middleware('can:role_permissions.update');
        Route::patch('/{permission}/deactivate', [PermissionsController::class, 'deactivate'])->name('deactivate')->middleware('can:role_permissions.update');

        // Vues spéciales
        Route::get('/grouped/by-module', [PermissionsController::class, 'byModule'])->name('by-module')->middleware('can:role_permissions.read');
        Route::get('/matrix/all', [PermissionsController::class, 'matrix'])->name('matrix')->middleware('can:role_permissions.read');

        // Génération CRUD automatique
        Route::post('/generate/crud', [PermissionsController::class, 'generateCrud'])->name('generate-crud')->middleware('can:role_permissions.create');
    });
});
