<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // user
    // organizations
    Route::prefix('/user/organizations')->group(function () {
        // store
        Route::post('/', 'OrganizationCreateController');
        // get list
        Route::get('/list', 'OrganizationsListController');
        // switch to another
        Route::put('/switch/{organization}', 'OrganizationSwitchController');
        // CURRENT ORGANIZATION MANIPULATIONS
        Route::prefix('/current')->group(function () {
            // get current
            Route::get('/', 'OrganizationCurrentGetController');
            // update current
            Route::put('/', 'OrganizationCurrentUpdateController');
            // delete current
            Route::delete('/', 'OrganizationCurrentDeleteController');
        });
        // WORKSPACES
        Route::prefix('/workspaces')->group(function () {
            // store
            Route::post('/', 'WorkspaceCreateController');
            // get list
            Route::get('/list', 'WorkspacesListController');
            // switch to another
            Route::put('/switch/{workspace}', 'WorkspaceSwitchController');
            // CURRENT ORGANIZATION MANIPULATIONS
            Route::prefix('/current')->group(function () {
                // get current
                Route::get('/', 'WorkspaceCurrentGetController');
                // update current
                Route::put('/', 'WorkspaceCurrentUpdateController');
                // delete current
                Route::delete('/', 'WorkspaceCurrentDeleteController');
            });
        });
        // MEMBERS
        Route::prefix('/members')->group(function () {
            Route::get('/', 'MembersListController');
            Route::put('/', 'MembersUpdateSettingsController');
            Route::delete('/{user}', 'MembersKickController');
            Route::prefix('/invite')->group(function () {
                Route::post('/', 'MembersInviteController');
                Route::middleware('signed:relative')->get('/accept/{user}/{organization}', 'MembersInviteAcceptController')->name('organizations.invite.member.accept');
                Route::middleware('signed:relative')->get('/decline/{user}/{organization}', 'MembersInviteDeclineController')->name('organizations.invite.member.decline');
            });
        });
    });
});
