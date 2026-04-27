<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setup-cpanel', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        return "Setup ran successfully! Migrations, storage link, and cache clear are done.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/seed-cpanel', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'AdminSeeder', '--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'SettingSeeder', '--force' => true]);
        return "Seeders ran successfully! You now have the Admin, Settings, and default Announcements.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
