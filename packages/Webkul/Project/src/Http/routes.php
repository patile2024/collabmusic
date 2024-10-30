<?php

use Illuminate\Support\Facades\Route;
use Webkul\Project\Http\Controllers\ProjectController;

Route::group([
        'prefix'        => 'admin/project',
        'middleware'    => ['web', 'user']
    ], function () {

        Route::get('', 'Webkul\Project\Http\Controllers\ProjectController@index')->name('admin.project.index');
        Route::get('projects', [ProjectController::class, 'index'])->name('category.project.index');;

});
