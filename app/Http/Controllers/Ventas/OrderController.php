<?php

namespace App\Http\Controllers\Ventas;


use App\Http\Controllers\Controller;
use App\Models\Ventas\Order;
use App\Models\Ventas\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends Controller
{
     public function index($id = 0){
        try{
            if($id == 0){
                $order_by = request('order_by') ? request('order_by') : 'id';

                $order_direction = request('order_direction') ? request('order_direction') : 'desc';

                $orders = Order::orderBy($order_by,$order_direction)
                                ->filtros()
                                ->filterFechas([
                                    'fecha_inicio' => request('fecha_inicio'),
                                    'fecha_fin' => request('fecha_fin')])
                                ->paginate(20);

            }else{
                //ordenes con  sus detalles y cliente
                $orders = Order::with('orderDetails','cliente')
                        ->where('id',$id)
                        ->first();
                //$orders = Order::find($id);
            }
            return response($orders,200);
        }
        catch (\Exception $exception){
            return response($exception->getMessage(),500);
        }
     }


    public function getCreditCardData(Request  $request){
        try {
          $order =  Order::where('access_token',$request->access_token)->first();
          return response($order->res_web_pay,200);
        }catch (Exception $exception){
            return response($exception,422);
        }
    }

}
