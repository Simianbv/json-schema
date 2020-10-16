<?php

use Illuminate\Support\Facades\Route;

$prefix = '/' . rtrim(trim(config('json-schema.prefix'), '/'), '/');

if ($prefix == '/' || $prefix == '//') {
    $prefix = '';
}

Route::group(['middleware' => ['api', 'introspect']], function () use ($prefix) {
    Route::get($prefix . '/schema/properties/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\SchemaController@getProperties');
    Route::get($prefix . '/schema/layout/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\SchemaController@getLayout');
    Route::get($prefix . '/schema/filters/{scope}/{resource?}', '\Simianbv\JsonSchema\Http\FilterController@index');

    Route::get($prefix . '/schema/filters/{model}', '\Simianbv\JsonSchema\Http\FilterController@getFiltersByModel');
    Route::get($prefix . '/schema/filters', '\Simianbv\JsonSchema\Http\FilterController@getFiltersByModel');
});


