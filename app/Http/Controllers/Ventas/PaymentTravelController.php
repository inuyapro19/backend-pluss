<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\AgenciaCookie;
use App\Models\Ventas\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentTravelController extends Controller
{
    // https://demo.recorrido.cl/api/v2/es/bookings/23c8zcab/book.json post
    public function  getBookingBook(Request $request){
        try {
            $token         = $request->access_token;
            $booking_token = $request->token;
            $total_price = $request->total_price;
            //obtiene la cookies de la agencia
            //obtiene la cookies de la agencia
            $cookieValue = ['_recorrido_session' => Cache::get('_recorrido_session')];;
            $cookieValueDomain = Cache::get('_recorrido_session_domain');

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->timeout(10)
                // ->withCookies($array_cookies,'demo.recorrido.cl')
                ->withCookies($cookieValue,$cookieValueDomain)
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
            $cookieValue = ['_recorrido_session' => Cache::get('_recorrido_session')];;
            $cookieValueDomain = Cache::get('_recorrido_session_domain');

            $response =  Http::withBasicAuth('plusschile','plusschile')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                //->withCookies($array_cookies,'demo.recorrido.cl')
                ->timeout(10)
                ->withCookies($cookieValue,$cookieValueDomain)
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
