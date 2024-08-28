<?php

namespace App\Http\Controllers\Ventas;



use App\Http\Controllers\Controller;
use App\Models\Ventas\Cliente;
use App\Models\Ventas\Order;
use App\Models\Ventas\OrderDetail;
use Illuminate\Http\Request;
use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    public function __construct(){
        if (app()->environment('production')) {
            WebpayPlus::configureForProduction(config('services.transbank.webpay_plus_cc'), config('services.transbank.webpay_plus_api_key'));
        } else {
            WebpayPlus::configureForTesting();
        }
    }

    public function createdTransaction(Request $request)
    {
        try {


            $url = env('APP_URL').'api/v1/pago/returnUrl';

            //crear orden
              $orden = Order::create([
                    'access_token'=>$request->access_token,
                    'api_token_compra'=>$request->token_compra,
                    'token_web_pay'=>'',
                    'fecha_compra'=>now(),
                    'fecha_confimacion_compra'=>now(),
                    'cantidad'=> $request->cantidad,
                    'total'=> $request->total_price,
                    'estado'=>1
                ]);

               $orden->number_order = 'PL-'.$this->zero_fill($orden->id,3);
               $orden->save();

            //crear orden detalle
            OrderDetail::create([
                'order_id'=>$orden->id,
                'detalles'=>$request->carro
            ]);

            //crear cliente y datos de comprador
           // $cliente = json_decode($request->pasajero);
            Cliente::create([
                'order_id'=>$orden->id,
                'tipo_documento'=>$request->tipo_documento,
                'documento'=>$request->documento,
                'nombre'=>$request->nombre,
                'apellidos'=>$request->apellido,
                'email'=>$request->email,
                'telefono'=>$request->telefono,
                'formulario'=>$request->formulario,
            ]);

            //$request = $request->except('_token');
            $respuestas = (new Transaction)->create(
                $orden->id,
                '1127555',
                $request->total_price,
                $url
            );

            //dd($respuestas);
            //log de la transacion creada
            Log::channel('webpay')->info('Transaccion creada',[
                'orden'=>$orden->id,
                'total'=>$request->total_price,
                'url'=>$url,
                'data'=>json_encode($respuestas)
            ]);
            return response(json_encode($respuestas),200);

        }catch (\Exception $exception){
            return response($exception,422);
        }

    }


    /*
     * zero_fill
     *
     * Rellena con ceros a la izquierda
     *
     * @param $valor valor a rellenar
     * @param $long longitud total del valor
     * @return valor rellenado
     */

    function zero_fill ($valor, $long = 0)
    {
        return str_pad($valor, $long, '0', STR_PAD_LEFT);
    }

    public function commitTransaction(Request $request)
    {
        $req = $request->except('_token');
        $token =$req["token_ws"] ?? $req['TBK_TOKEN'];

        if (array_key_exists('token_ws',$req)){

            $resp = (new Transaction)->commit($req["token_ws"] );


            if ($resp->responseCode == 0){
                $order = Order::where('id',$resp->buyOrder)->update([
                    'token_web_pay'=>$token,
                    'fecha_confimacion_compra'=>now(),
                    'res_web_pay'=>json_encode($resp),
                    'estado'=>0
                ]);

                //log de la respuesta de la transacion
                Log::channel('webpay')->info('Transaccion exitosa',[
                    'orden'=>$resp->buyOrder,
                    'total'=>$resp->amount,
                    'token'=>$token,
                    'estado'=>0,
                    'respuesta'=>$resp,
                    'fecha'=>now()
                ]);

                return redirect(env('APP_FRONT_URL').'#/comprobar-pago?CODE='.$req["token_ws"]);

                //return response('exito',200);

            }else{
                $order = Order::where('id',$resp->buyOrder)->update([
                    'token_web_pay'=>$token,
                    'fecha_confimacion_compra'=>now(),
                    'res_web_pay'=>json_encode($resp),
                    'estado'=>2
                ]);

                //log en caso de error en la transacion
                Log::channel('webpay')->info('Transaccion fallida',[
                    'orden'=>$resp->buyOrder,
                    'total'=>$resp->amount,
                    'token'=>$token,
                    'estado'=>2,
                    'respuesta'=>$resp,
                    'fecha'=>now()
                ]);

                return redirect(env('APP_FRONT_URL').'#/pago-error?CODE='.$req["token_ws"]);

                // return response('error ',422);

            }

        }else{
            $resp = $req;
            $order = Order::where('id',$resp["TBK_ORDEN_COMPRA"])->update([
                'token_web_pay'=>$token,
                'fecha_confimacion_compra'=>now(),
                'res_web_pay'=>json_encode($resp),
                'estado'=>3
            ]);

            //log en caso de error en la transacion
            Log::channel('webpay')->info('Transaccion fallida',[
                'orden'=>$resp["TBK_ORDEN_COMPRA"],
                'total'=>$resp["TBK_MONTO"],
                'token'=>$token,
                'estado'=>3,
                'respuesta'=>$resp,
                'fecha'=>now()
            ]);

            return redirect(env('APP_FRONT_URL').'#/pago-error?CODE='.$token);

            //  return response($req,422);

        }
    }

    public function cancelado(Request $request)
    {

        $req = $request->all();

       // return response(json_encode($req),200);

       // return redirect(env('APP_FRONT_URL').'#/pago-error');

        return response('error cancelado',422);

    }

    public function cancelado_vista(){

        return redirect(env('APP_FRONT_URL').'#/pago-error');

    }

    public function showRefund()
    {
        return view('webpayplus/refund');
    }

   public function refundTransaction(Request $request)
    {
        //$error = false;
        //return response($request);
        //buscar en order el token

        try {
            $req = $request->except('_token');

            $order = Order::where('api_token_compra', $request->token_compra)
                            ->first();

            $token_webpay = $order->token_web_pay;
            $total = $order->total;
           // return response($token_webpay,200);

            $response = (new Transaction)->refund($token_webpay, $total);

            //return response($response,200);

            $order->res_web_pay = json_encode($response);
            $order->estado = 4; //reembolso
            $order->save();

            Log::channel('webpay-refund')->info('Transaccion refutada',[
                //'orden'=>$resp["TBK_ORDEN_COMPRA"],
                'total'=>$total,
                'token_compra'=>$request->token_compra,
                'estado'=>4,
                'respuesta'=>$response,
                'fecha'=>now()
            ]);


            return response($response,200);

        } catch (\Exception $e) {
            $resp = array(
                'respuesta'=>$response,
                'msg' => $e->getMessage(),
                'code' => $e->getCode()
            );

            Log::channel('webpay-refund')->info('Error al hacer la reversa de dinero',[
                'total'=>$total,
                'token_compra'=>$request->token_compra,
                'estado'=>'-4',
                'respuesta'=>$response,
                'fecha'=>now(),
                'msg' => $e->getMessage(),
                'status' => $e->getCode()
            ]);
            return response($resp,500);
           //$error = true;
        }

        //return redirect(env('APP_FRONT_URL').'#/pago-error?CODE='.$token);

    }

    public function getTransactionStatus(Request $request)
    {
        $req = $request->except('_token');
        $token = $req["token"];

        $resp = (new Transaction)->status($token);

        return view('webpayplus/transaction_status', ["resp" => $resp, "req" => $req]);
    }
}
