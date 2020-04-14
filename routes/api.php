<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'introspect']], function () {

    Route::get('/api/schema/properties/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\SchemaController@getProperties');
    Route::get('/api/schema/layout/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\SchemaController@getLayout');
    Route::get('/api/schema/filters/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\FilterController@index');

    Route::get('/api/filters/{model}',  '\Simianbv\JsonSchema\Http\FilterController@getFiltersByModel');
    Route::get('/api/filters',          '\Simianbv\JsonSchema\Http\FilterController@getFiltersByModel');


});


