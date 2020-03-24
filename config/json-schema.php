<?php

return [

    /*
     * To enable or disable logging
     */
    'logging' => true,
    'log_path' => storage_path('logs/json'),
    'base_url' => env('APP_URL'),

    /*
     * Define te resource paths, both namespace and physical path
     */
    'resources' => [
        'path' => app_path('Resources/Json'),
        'namespace' => "App\\Resources\\Json\\",
        'facade_namespace' => "\\Simianbv\\JsonSchema\\Fields\\Facades\\",
    ],

    /*
     * Define the controller paths, both namespace and physical path
     */
    'controllers' => [
        'path' => app_path('Http/Controllers/Api'),
        'namespace' => 'App\\Http\\Controllers\\',
    ],

    /*
     * Define the model paths, both namespace and physical path
     */
    'models' => [
        'path' => app_path('Models'),
        'namespace' => "App\\Models\\",
    ],

    'overview' => [
        'location' => resource_path('views/generated')
    ],
    'forms' => [
        'location' => resource_path('views/generated')
    ],

    /*
     * Expose all the stub files to be used
     */
    'stubs' => [
        'model'                 => resource_path('stubs/model.stub'),
        'model-relation'        => resource_path('stubs/model-relation.stub'),
        'model-soft-delete'     => resource_path('stubs/model-soft-delete.stub'),
        'controller'            => resource_path('stubs/controller.stub'),
        'controller-has-many'   => resource_path('stubs/controller-has-many.stub'),
        'controller-has-one'    => resource_path('stubs/controller-as-one.stub'),
        'migration'             => resource_path('stubs/migration.stub'),
        'resource'              => resource_path('stubs/resource.stub'),
        'form'                  => resource_path('stubs/form.stub'),
        'overview'              => resource_path('stubs/overview.stub'),
        'overview-column'       => resource_path('stubs/overview-column.stub'),
        'overview-detail'       => resource_path('stubs/overview-detail.stub'),
    ],

    /*
     * Define the yaml base path, where to search for the .yaml/.yml files
     */
    'yaml' => [
        'path' => resource_path('assets/json-schema/'),
    ],

];
