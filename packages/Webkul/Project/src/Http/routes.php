<?php

Route::group([
        'prefix'        => 'admin/project',
        'middleware'    => ['web', 'user']
    ], function () {

        Route::get('', 'Webkul\Project\Http\Controllers\ProjectController@index')->name('admin.project.index');

});