<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\AgenciaCookie;
use App\Models\Ventas\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchTravelController extends Controller
{
    public function getCities(){
        try {
            /* $response =  Http::withBasicAuth('plusschile','plusschile')
                 ->withHeaders([
                     'x-filter-with-bo'=>229
                 ])
                 ->timeout(10)
                 ->get(env('API_RECORRIDO_URL').'/api/v2/es/cities.json?country=chile');*/

            //inserta en model cities
            /*   $cities = json_decode($response->getBody(),true);
               foreach ($cities as $item){
                   Cities::create([
                       'id' => $item->id,
                       'url_name' => $item->url_name,
                       'name' => $item->name,
                       'latitude' => $item->latitude,
                       'longitude' => $item->longitude,
                       'url' => $item->url,
                       'country_id' => $item->country_id
                   ]);
               }*/

            //devuelve desde model cities ordenado por nombre y selecciona el nombre y id para enviar a la vista
            $cities_all = Cities::orderBy('name','asc')
                ->select('id','name')
                ->get();

            return response(json_encode($cities_all),200);

        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    //api/v2/es/bus_travels.json POST
    public function getBusTravel(Request $request){
        try {

            $request->validate([
                'departure_city_id' => 'required',
                'destination_city_id' => 'required',
                // 'access_token' => 'required'
            ]);
            $cookieValue = ['_recorrido_session' => Cache::get('_recorrido_session')];;
            $cookieValueDomain = Cache::get('_recorrido_session_domain');
            //return response($cookieValueDomain,200);

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-filter-with-bo'=>229
                ])
                ->timeout(10)
                ->withCookies($cookieValue,$cookieValueDomain)
                ->post(env('API_RECORRIDO_URL').'/api/v2/es/bus_travels.json',[
                    "bus_travel"=> [
                        "departure_city_id"=> $request->departure_city_id,
                        "destination_city_id"=> $request->destination_city_id,
                        "departure_date"=> $request->departure_date
                    ]
                ]);

            Log::channel('file')->info('Busqueda de viajes', [
                'PASO'=>'2',
                'information' =>$response->getBody() ,
            ]);

            return response($response->body(),200);

        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    // /api/v2/es/bus_travels/{{ _.bus_travel_id }}/directions/outbound/bus_operators/{{ _.bus_operator_id }}/search_results.json get

    public function getBusTravelSearchResults(Request $request){
        try {

            //$token = $request->access_token;
            //obtiene la cookies de la agencia
            //$cookies = AgenciaCookie::where('access_token','=',$token)->first();
            $cookieValue = ['_recorrido_session' => Cache::get('_recorrido_session')];;
            $cookieValueDomain = Cache::get('_recorrido_session_domain');
            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'x-filter-with-bo'=>229
                ])
                ->timeout(10)
                ->withCookies($cookieValue,$cookieValueDomain)
                ->get(env('API_RECORRIDO_URL').'/api/v2/es/bus_travels/'.$request->bus_travel_id.'/directions/outbound/bus_operators/'.$request->bus_operator_id.'/search_results.json');

            Log::channel('file')->info('Resultado de la busqueda', [
                'PASO'=>'2',
                'information' =>$response->getBody() ,
            ]);

            return response($response->body(),200);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }


    }
}
