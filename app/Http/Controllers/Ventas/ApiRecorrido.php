<?php

namespace App\Http\Controllers\Ventas;



use App\Http\Controllers\Controller;
use App\Models\Ventas\AgenciaCookie;
use App\Models\Ventas\Cities;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

class ApiRecorrido extends Controller
{
    // auth post agency.json api with demo.recorrido https://demo.recorrido.cl/api/v2/es/agencies/sign_in.json GET

    private $dominio;

    public function __construct(){
        if (app()->environment('production')) {
            $this->dominio = 'recorrido.cl';
        } else {
            $this->dominio = 'new.recorrido.club';
        }
    }

    // auth post agency.json api with demo.recorrido https://demo.recorrido.cl/api/v2/es/agencies/sign_in.json GET
    public function signInAgency(){
        try {

            $response = Http::withBasicAuth('plusschile', 'plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->timeout(10)
                ->post(env('API_RECORRIDO_URL').'/api/v2/es/agencies/sign_in.json', [
                            "agency"=> [
                                "email"=> env('API_AGENCY_USERNAME'),
                                "password"=> env('API_AGENCY_PASSWORD')
                            ]
                ]);
            $cookies1 = $response->cookies()->getCookieByName('_recorrido_session');
            $cookies2 = $response->cookies()->getCookieByName('_recorrido_session')->getPath();
            $dominio = $response->cookies()->getCookieByName('_recorrido_session')->getDomain();




           $array_cookies = [
               '_recorrido_session' => $cookies1->getValue()
           ];

            $data = $this->getAuthorizationUrl($array_cookies,$dominio);
            $data2 = json_decode($data->getContent(),true);
            $code = $data2['code'];

            $resToken = $this->getAccessToken($array_cookies,$dominio,$code);
            $token = json_decode($resToken->getContent(),true);
            AgenciaCookie::create([
                'name'=>'_recorrido_session',
                'valor' => $cookies1->getValue(),
                'access_token'=> $token['access_token']
            ]);

            //log de sistema para ver si se creo la session
            Log::channel('file')->info('Se creo la session de la api', [
                'PASO'=>'1',
                'information' =>$resToken->getContent() ,
            ]);

            return response($resToken->getContent(),200);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }

    }
        // /oauth/authorize.json POST
    public function getAuthorizationUrl($array_cookies,$dominio){
        try {

            $response = Http::withBasicAuth('plusschile', 'plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])->withCookies($array_cookies,$dominio)
                ->timeout(10)
                ->post(env('API_RECORRIDO_URL').'/oauth/authorize.json', [
                    "client_id"=> env('API_CLIENT_ID'),
                    "redirect_uri"=> env('API_RECORRIDO_URL'),
                    "response_type"=> "code"
                ]);
            Log::channel('file')->info('Se creo la codigo de acceso de la api', [
                'PASO'=>'2',
                'information' =>$response->getBody() ,
            ]);
            return response($response->getBody(), 200);

        } catch (\Exception $exception) {

            return response()->json([
                'error' => $exception->getMessage()
            ], 500);

        }
    }

    ///oauth/token.json //POST
    public function getAccessToken($array_cookies, $dominio, $code){
        try {


            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])
                ->timeout(10)
                ->post(env('API_RECORRIDO_URL').'/oauth/token.json', [
                    "code"=>$code,
                    "client_id"=> env('API_CLIENT_ID'),
                    "client_secret"=> env('API_CLIENT_SECRET'),
                    'redirect_uri'=> env('API_RECORRIDO_URL'),
                    "grant_type"=> "authorization_code"
                ]);

            //dd($response->getBody());
            Log::channel('file')->info('Se creo la token de acceso de la api', [
                'PASO'=>'2',
                'information' =>$response->getBody() ,
            ]);

            return response($response->getBody(), 200);

        } catch (\Exception $exception) {

            return response()->json([
                'error' => $exception->getMessage()
            ], 500);

        }
    }

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
            $token = $request->access_token;
            //obtiene la cookies de la agencia
            $cookies = AgenciaCookie::where('access_token','=',$token)->first();
            $array_cookies = [
                '_recorrido_session' => $cookies->valor
            ];
            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-filter-with-bo'=>229
                ])
                ->timeout(10)
                ->withCookies($array_cookies,$this->dominio)
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

            $token = $request->access_token;
            //obtiene la cookies de la agencia
            $cookies = AgenciaCookie::where('access_token','=',$token)->first();
            $array_cookies = [
                '_recorrido_session' => $cookies->valor
            ];
            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'x-filter-with-bo'=>229
                ])
                ->timeout(10)
                ->withCookies($array_cookies,$this->dominio)
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

    // https://demo.recorrido.cl/api/v2/es/bookings.json POST
    public function getBooking(Request $request){
        try {

          // return response($this->dominio);

           $token = $request->access_token;
           //obtiene la cookies de la agencia
           $cookies = AgenciaCookie::where('access_token','=',$token)->first();
           $array_cookies = [
                '_recorrido_session' => $cookies->valor
            ];

           //return  response($array_cookies);

           $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-filter-with-bo'=>229
                ])
                //->withCookies($array_cookies,'demo.recorrido.cl')
                   // ->timeout(10)
               ->withCookies($array_cookies,$this->dominio)
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
            $cookies = AgenciaCookie::where('access_token','=',$token)->first();
            $array_cookies = [
                '_recorrido_session' => $cookies->valor
            ];

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                //->withCookies($array_cookies,'demo.recorrido.cl')
                ->withCookies($array_cookies,$this->dominio)
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
            $cookies = AgenciaCookie::where('access_token','=',$token)->first();
            $array_cookies = [
                '_recorrido_session' => $cookies->valor
            ];

           //return response($request,200);

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->withCookies($array_cookies,$this->dominio)
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
     public function   getBookingPassengersPost(Request $request){
        try {
            $token         = $request->access_token;
            $booking_token = $request->booking_token;

            //obtiene la cookies de la agencia
            $cookies = AgenciaCookie::where('access_token','=',$token)->first();
            $array_cookies = [
                '_recorrido_session' => $cookies->valor
            ];

            //return response($request->pasajero->access_token,200);

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                //->withCookies($array_cookies,'demo.recorrido.cl')
                    ->timeout(10)
                ->withCookies($array_cookies,$this->dominio)
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

    // https://demo.recorrido.cl/api/v2/es/bookings/23c8zcab/book.json post
    public function  getBookingBook(Request $request){
        try {
            $token         = $request->access_token;
            $booking_token = $request->token;
            $total_price = $request->total_price;
            //obtiene la cookies de la agencia
            $cookies = AgenciaCookie::where('access_token','=',$token)->first();
            $array_cookies = [
                '_recorrido_session' => $cookies->valor
            ];

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->timeout(10)
               // ->withCookies($array_cookies,'demo.recorrido.cl')
               ->withCookies($array_cookies,$this->dominio)
                ->post(env('API_RECORRIDO_URL').'/api/v2/es/bookings/'.$booking_token.'/book.json',[
                    "access_token"=> $token,
                    "locale"=> "es",
                    "total_price"=> $total_price,
                    "token"=>$booking_token
                ]);

               $order = Order::where('api_token_compra','=',$booking_token)->update([
                    'res_compra_recorridos' => json_encode($response->body())
                ]);

            Log::channel('recorrido-error')->info('Valida si la compra fue exitosa o hubo un error', [
                'PASO'=>'4',
                'information' =>$response->getBody() ,
            ]);

            return response($response->body(),200);

        }catch (\Exception $exception){

            $order = Order::where('api_token_compra','=',$booking_token)->update([
                'res_compra_recorridos' => json_encode($response->body())
            ]);

            Log::channel('recorrido-error')->info('ERROR AL PROCESAR LA SOLICITUD DE COMPRA FINALIZADA', [
                'PASO'=>'4',
                'token'=>$booking_token,
                'information' =>$exception,
                'total_price'=>$total_price,
                'estado'=> $exception->getCode()
            ]);
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    //get booking pdf
    public function getBookingPdf(Request $request){
        try {
            $token         = $request->access_token;
            $booking_token = $request->token;
            //obtiene la cookies de la agencia
            $cookies = AgenciaCookie::where('access_token','=',$token)->first();
            $array_cookies = [
                '_recorrido_session' => $cookies->valor
            ];
            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                //->withCookies($array_cookies,'demo.recorrido.cl')
                    ->timeout(10)
              ->withCookies($array_cookies,$this->dominio)
                ->get(env('API_RECORRIDO_URL').'/api/v2/es/bookings/'.$booking_token.'.pdf',[
                    "access_token"=> $token,
                ]);

            //convertir pdf a base64
            $pdf = base64_encode($response->body());

            //DOWNLOAD PDF BASE 64
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="file.pdf"',
            ];

           // return response($pdf,200);

           return response($response->body(),200);

        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    //cancelar booking
    public function cancelBooking(Request $request){
        try {
            //autorizcion para cancelar
            if ($request->rol !== 'admin'){
                return response('No tiene autorizacion para cancelar',422);
            }

            $token          = $request->access_token;
            $booking_token  = $request->token;
            $ticket_id      = $request->ticket_id;
            $razon          = $request->reason_of_cancellation;

            //obtiene la cookies de la agencia
            $response =  Http::withBasicAuth('plusschile','plusschile')
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                        ])
                        ->timeout(10)
                        ->post(env('API_RECORRIDO_URL').'/api/v2/es/bookings/'.$booking_token.'/tickets/'.$ticket_id.'/cancel.json',[
                            "token"=> $booking_token,
                            "ticket_id"=> $ticket_id,
                            "reason_of_cancellation"=> $razon,
                            "access_token"=>$token
                        ]);

            return response($response->body(),200);

        }catch (\Exception $exception){

            return response()->json([
                'error' => $exception->getMessage()
            ], 500);

        }
    }

    // send email pdf booking
    public function sendEmailPdfBooking(Request $request){
        try{
            $request->validate([
                'email' => 'required|email',
                'token' => 'required',
                'access_token' => 'required'
            ]);
            $email = $request->email;
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }



}
