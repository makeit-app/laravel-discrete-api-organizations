<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // user
    // organizations
    Route::prefix('/user/organizations')->group(function () {
        // get list
        Route::get('/list', 'OrganizationsListController');
        // switch to another
        Route::put('/switch', 'OrganizationSwitchController');
        // CURRENT ORGANIZATION MANIPULATIONS
        Route::prefix('/current')->group(function () {
            // get current
            Route::get('/', 'OrganizationCurrentController');
            // update current
            Route::put('/', 'OrganizationCurrentUpdateController');
            // delete current
            Route::delete('/', 'OrganizationCurrentDeleteController');
        });
    });
});
