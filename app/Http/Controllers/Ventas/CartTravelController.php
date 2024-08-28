<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\AgenciaCookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartTravelController extends Controller
{
    // https://demo.recorrido.cl/api/v2/es/bookings.json POST
    public function getBooking(Request $request){
        try {

            // return response($this->dominio);

            $token = $request->access_token;
            //obtiene la cookies de la agencia
            $cookieValue = ['_recorrido_session' => Cache::get('_recorrido_session')];;
            $cookieValueDomain = Cache::get('_recorrido_session_domain');

            //return  response($array_cookies);

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-filter-with-bo'=>229
                ])
                //->withCookies($array_cookies,'demo.recorrido.cl')
                // ->timeout(10)
                ->withCookies($cookieValue,$cookieValueDomain)
                ->post(env('API_RECORRIDO_URL').'/api/v2/es/bookings.json',[
                    "booking"=> [
                        "outbound_search_result_id"=> $request->outbound_search_result_id,
                        "bus_travel_id"=> $request->bus_travel_id
                    ],
                    "access_token"=>$token
                ]);

            /* LogSistema::create([
                   'accion_api',
                   'tabla_api',
                   'registro_id',
                   'registro_api_new',
                   'registro_api_old'
               ]);*/
            Log::channel('file')->info('Valida si la compra fue exitosa o hubo un error', [
                'PASO'=>'pago exitoso o error',
                //'datos' =>$response->getBody(),
                'datos' =>$response->json(),
                'status' =>$response->status(),
            ]);

            return response($response->body(),200);
        }catch (\Exception $exception){

            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }
    // https://demo.recorrido.cl/api/v2/es/bookings/{{ _.booking_id }}/passengers.json GET
    public function getBookingPassengers(Request $request){
        try {

            $token         = $request->access_token;
            $bookien_token = $request->booking_token;
            //obtiene la cookies de la agencia
            $cookieValue = ['_recorrido_session' => Cache::get('_recorrido_session')];;
            $cookieValueDomain = Cache::get('_recorrido_session_domain');


            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                //->withCookies($array_cookies,'demo.recorrido.cl')
                ->withCookies($cookieValue,$cookieValueDomain)
                ->timeout(10)
                ->get(env('API_RECORRIDO_URL').'/api/v2/es/bookings/'.$bookien_token.'/seats.json',[
                    'access_token'=>$token
                ]);

            Log::channel('file')->info('obtiene mapa de los asientos del bus', [
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

    //https://demo.recorrido.cl/api/v2/es/bookings/23c8zcab/seats.json post
    public function getBookingSeats(Request $request){
        try {
            $token         = $request->access_token;
            $bookien_token = $request->booking_token;
            $seats = $request->seats;
            //obtiene la cookies de la agencia
            $cookieValue = ['_recorrido_session' => Cache::get('_recorrido_session')];;
            $cookieValueDomain = Cache::get('_recorrido_session_domain');


            //return response($request,200);

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->withCookies($cookieValue,$cookieValueDomain)
                ->timeout(10)
                ->post(env('API_RECORRIDO_URL').'/api/v2/es/bookings/'.$bookien_token.'/seats.json',[
                    "booking"=> [
                        "seat_numbers_outbound"=> $seats
                    ],
                    "access_token"=> $token
                ]);

            Log::channel('file')->info('SELECCION DE ASIENTOS', [
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

    // https://demo.recorrido.cl/api/v2/es/bookings/23c8zcab/passengers.json post
    public function getBookingPassengersPost(Request $request){
        try {
            $token         = $request->access_token;
            $booking_token = $request->booking_token;

            //obtiene la cookies de la agencia
            $cookieValue = ['_recorrido_session' => Cache::get('_recorrido_session')];;
            $cookieValueDomain = Cache::get('_recorrido_session_domain');

            //return response($request->pasajero->access_token,200);

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                //->withCookies($array_cookies,'demo.recorrido.cl')
                ->timeout(10)
                ->withCookies($cookieValue,$cookieValueDomain)
                ->post(env('API_RECORRIDO_URL').'/api/v2/es/bookings/'.$booking_token.'/passengers.json',[
                    "access_token"=> $token,
                    "locale"=> "es",
                    "booking"=> [
                        "payment_method"=> "agency",
                        "currency"=> "CLP",
                        "email"=> $request->email,
                        "phone"=> $request->telefono,
                        "address"=> '',
                        "city"=> '',
                        "first_name"=> $request->nombres,
                        "last_name"=> $request->apellidos,
                        "tickets"=> $request->ticket,
                        "api_data_outbound"=> $request->api_data_outbound,
                    ]
                ]);

            Log::channel('file')->info('Envio de formulario de pasajeros', [
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
