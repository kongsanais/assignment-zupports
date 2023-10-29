<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\GoogleMapApiController;

/*
route  = api/v1/googleMapApi
*/
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function (){
    /* Define api route */
    Route::get('googleMapApi/placeSearch/{textsearch}', [GoogleMapApiController::class, 'searchPlaceByText']);
    Route::get('googleMapApi/nearbySearch/{lat}/{lng}/{type}', [GoogleMapApiController::class, 'searchLocationByLatLngAndType']);
});



