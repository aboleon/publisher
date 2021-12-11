<?php declare(strict_types=1);

# Ajax posts

Route::post('ajax', [\Aboleon\Publisher\Http\Controllers\AjaxController::class, 'distribute'])->name('ajax');


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
/*
# Page edit routes
Route::prefix('pages/edit/{id}/subpage/{param}')->group(function () {
    Route::get('list/{listable}', function ($id, $listable) {
        return Pages::subpageList($id, $listable);
    });
    Route::any('/', function ($id, $param) {
        return Pages::subpage($id, $param);
    });
});

Route::get('pages/list/{var}', [ListableController::class, 'basic'])->name('list_basic');
Route::get('pages/list/{var}/parent/{type}', [ListableController::class, 'basic']);
Route::any('pages/list/{var}/create/{parent?}', [ListableController::class, 'createBasic'])->name('basic_list_create');
Route::post('pages/list/store', [ListableController::class, 'storeBasic']);

Route::get('pages/edit/{id}', [PagesController::class, 'edit'])->name('edit');
Route::post('pages/edit/{id}', [PagesController::class, 'store'])->name('store');

# General distribution route
Route::any('{object}/{action}/{var?}/{param?}', [PanelController::class, 'distribute']);
*/
