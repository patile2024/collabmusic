<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\projects\ActivityController;
use Webkul\Admin\Http\Controllers\Project\ProjectController;

Route::group(['middleware' => ['user'], 'prefix' => config('app.admin_path')], function () {
    Route::controller(ProjectController::class)->prefix('projects')->group(function () {
        Route::get('', 'index')->name('admin.projects.index');

        Route::get('create', 'create')->name('admin.projects.create');

        Route::post('create', 'store')->name('admin.projects.store');

        Route::get('view/{id}', 'view')->name('admin.projects.view');

        Route::get('edit/{id}', 'edit')->name('admin.projects.edit');

        Route::put('edit/{id}', 'update')->name('admin.projects.update');

        Route::get('search', 'search')->name('admin.projects.search');

        Route::get('{id}/warehouses', 'warehouses')->name('admin.projects.warehouses');

        Route::post('{id}/inventories/{warehouseId?}', 'storeInventories')->name('admin.projects.inventories.store');

        Route::delete('{id}', 'destroy')->name('admin.projects.delete');

        Route::post('mass-destroy', 'massDestroy')->name('admin.projects.mass_delete');
    });
});
