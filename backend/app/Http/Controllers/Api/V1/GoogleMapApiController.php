<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
/*
 Import guzzle
 document : https://docs.guzzlephp.org/en/stable/quickstart.html
*/
use GuzzleHttp\Client;

/*
  GoogleMapApiController
  Description : Controller to Call Google Map Api
*/
class GoogleMapApiController extends Controller
{
    /*
      author : Hatsanai
      create date : 2023-10-28
      description  : search place with text
    */
    public function searchPlaceByText(string $textsearch){
        /*get api key from  env() */
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        /*guzzleHttp  http client */
        $client = new Client();
        /*call google map api */
        /*document : https://developers.google.com/maps/documentation/places/web-service/search-text */
        $response = $client->get("https://maps.googleapis.com/maps/api/place/textsearch/json?query=$textsearch&key=$apiKey");
        /*check response from google map api*/
        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } else {
            return response()->json(['error' => 'Failed to fetch data from Google Maps API'], 500);
        }
    }

    /*
      author : Hatsanai
      create date : 2023-10-28
      description  : search with lat,lng,type
    */
    public function searchLocationByLatLngAndType(string $lat, string $lng, string $type) {
        /* get api key from  env() */
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        /*guzzleHttp  http client */
        $client = new Client();
        /*call google map api (nearby search )*/
        /*document : https://developers.google.com/maps/documentation/places/web-service/search-nearby */
        $response = $client->get("https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$lat,$lng&radius=2000&type=$type&key=$apiKey");
         /*check response from google map api*/
        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } else {
            return response()->json(['error' => 'Failed to fetch data from Google Maps API'], 500);
        }
    }
}

