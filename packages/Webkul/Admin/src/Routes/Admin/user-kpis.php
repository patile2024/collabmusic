<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Settings\GroupController;
use Webkul\Admin\Http\Controllers\Settings\SettingController;
use Webkul\Admin\Http\Controllers\User\UserKpiController;

/**
 * Settings routes.
 */
Route::prefix('user_kpis')->group(function () {
    /**
     * Settings main display page.
     */
    Route::get('', [UserKpiController::class, 'index'])->name('admin.user_kpi.index');
    Route::post('/import', [UserKpiController::class, 'store'])->name('admin.user_kpi.import');


});
