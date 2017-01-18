<?php

$router->group(['namespace' => 'Lite\Settings'], function ($router) {
//    $router->get('lite/settings', [
//        'as'   => 'lite.settings.index',
//        'uses' => 'SettingsController@index'
//    ]);
    $router->get('lite/settings', [
        'as'   => 'lite.settings.edit',
        'uses' => 'SettingsController@edit'
    ]);
    $router->put('lite/settings/store', [
        'as'   => 'lite.settings.store',
        'uses' => 'SettingsController@store'
    ]);
});