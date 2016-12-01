<?php

$router->group(
    ['namespace' => 'Complete\Xml'],
    function ($router) {
        $router->get(
            '/xml-import',
            [
                'as'   => 'xml-import.index',
                'uses' => 'XmlImportController@index'
            ]
        );

        $router->post(
            'xml-import',
            [
                'as'   => 'xml-import.store',
                'uses' => 'XmlImportController@store'
            ]
        );

        $router->get(
            '/xml-import/import-status',
            [
                'as'   => 'xml-import.status',
                'uses' => 'XmlImportController@status'
            ]
        );

        $router->get(
            '/xml-import/isCompleted',
            [
                'as'   => 'xml-import.isCompleted',
                'uses' => 'XmlImportController@isCompleted'
            ]
        );

        $router->get(
            '/xml-import/complete',
            [
                'as'   => 'xml-import.complete',
                'uses' => 'XmlImportController@complete'
            ]
        );
    }
);
