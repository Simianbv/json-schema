<?php

use Illuminate\Support\Facades\Route;

Route::group(
    ['middleware' => 'api'], function () {
    Route::get('/schema/properties/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\SchemaController@getProperties');
    Route::get('/schema/layout/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\SchemaController@getLayout');
    Route::get('/schema/filters/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\FilterController@index');
}
);


