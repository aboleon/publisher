<?php

use Aboleon\Publisher\Http\Controllers\{
    AjaxController,
    LaunchpadController,
    PublisherController};
use Illuminate\Support\Facades\Route;

# Ajax posts
Route::post('ajax', [AjaxController::class, 'distribute'])->name('ajax');

# Export files routes
Route::prefix('exports')->group(function () {
    Route::get('{tunnel}/{export_class}', function ($tunnel, $export_class) {
        return (new self())->exports($tunnel, ucfirst($export_class));
    });
});

Route::resource('launchpad', LaunchpadController::class);
Route::resource('pages', PublisherController::class);
Route::resource('launchpad.pages', PublisherController::class)->shallow()->scoped([
    'launchpad' => 'type',
]);
Route::get('editable/{type}', [PublisherController::class, 'editable'])->name('editable');