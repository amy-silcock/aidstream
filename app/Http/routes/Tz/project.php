<?php

$router->group(['domain' => 'tz.' . env('HOST'), 'namespace' => 'Tz'], function ($router) {
    $router->group(['namespace' => 'Project'], function ($router) {
        $router->resource('project', 'ProjectController');
        $router->get(
            '/project/upload',
            [
                'as'   => 'project.upload',
                'uses' => 'ProjectController@upload'
            ]
        );

        $router->get(
            'change-project-defaults/{projectId}',
            [
                'as'   => 'change-project-defaults',
                'uses' => 'ProjectController@changeProjectDefaults'
            ]
        );

        $router->patch(
            'override-project-default/{projectId}',
            [
                'as'   => 'project.override-project-default',
                'uses' => 'ProjectController@overrideProjectDefaults'
            ]
        );
    });
});
