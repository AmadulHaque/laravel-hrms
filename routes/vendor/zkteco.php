<?php

use App\Http\Controllers\ZKTeco\DeviceController;
use App\Http\Controllers\ZKTeco\ZKTecoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'plan.access', 'permission:manage-biomatric-attedance-settings'])->group(function () {
    Route::controller(DeviceController::class)->group(function () {
        Route::post('settings/zekto/devices', 'store')->name('settings.zekto.devices.store');
        Route::put('settings/zekto/devices/{device}', 'update')->name('settings.zekto.devices.update');
        Route::delete('settings/zekto/devices/{device}', 'destroy')->name('settings.zekto.devices.destroy');
    });
});




Route::controller(ZKTecoController::class)->group(function () {

    Route::get('/iclock/getrequest', 'getrequest');
    Route::get('/iclock/ping', 'ping');
    Route::post('/iclock/cdata', 'cdata');
    Route::get('/iclock/cdata', 'getCdata');

});
