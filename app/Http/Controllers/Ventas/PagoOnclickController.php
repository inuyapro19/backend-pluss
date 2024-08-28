<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use mysql_xdevapi\Exception;
use Transbank\Webpay\Options;
use Transbank\Webpay\Oneclick\MallInscription;
use Transbank\Webpay\Oneclick\MallTransaction;
use Transbank\Webpay\Oneclick;
class PagoOnclickController extends Controller
{
    public function __construct(){
        if (app()->environment('production')) {
            Oneclick::configureForProduction(config('services.transbank.oneclick_mall_cc'), config('services.transbank.oneclick_mall_api_key'));
        } else {
            Oneclick::configureForTesting();
        }
    }


    public function startInscription(Request $request)
    {
        try{
            session_start();

            $req = $request->except('_token');
            $userName = $req["user_name"];
            $email = $req["email"];
            $responseUrl = $req["response_url"];


            $resp = (new MallInscription)->start($userName, $email, $responseUrl);

            $_SESSION["user_name"] = $userName;
            $_SESSION["email"] = $email;
            return response(['resp' => $resp, 'req' => $req],200);
        }catch (Exception $e){
            return response(['error' => $e->getMessage()],500);
        }
    }

    public function finishInscription(Request $request)
    {
        try{
            session_start();
            $req = $request->except('_token');
            $token = $req["TBK_TOKEN"];

            $resp = (new MallInscription)->finish($token);
            $userName = array_key_exists("user_name", $_SESSION) ? $_SESSION["user_name"] : '';
            return response( ["resp" => $resp, "req" => $req, "username" => $userName],200);
        }catch (Exception $e){
            return response(['error' => $e->getMessage()],500);
        }

    }

    public function authorizeMall(Request $request)
    {
        try{
            session_start();
            $req = $request->except('_token');

            $userName = $req["username"];
            $tbkUser = $req["tbk_user"];
            $parentBuyOrder = $req["buy_order"];
            $childBuyOrder = $req["details"][0]["buy_order"];
            $amount = $req["details"][0]["amount"];
            $installmentsNumber = $req["details"][0]["installments_number"];
            $childCommerceCode = $req["details"][0]["commerce_code"];

            $details = [
                [
                    "commerce_code" => $childCommerceCode,
                    "buy_order" => $childBuyOrder,
                    "amount" => $amount,
                    "installments_number" => $installmentsNumber
                ]
            ];


            $resp = (new MallTransaction)->authorize($userName, $tbkUser, $parentBuyOrder, $details);

            return response(["req" => $req, "resp" => $resp],200);
        }catch (Exception $e){
            return response(['error' => $e->getMessage()],500);
        }

    }

    public function transactionStatus(Request $request)
    {
       try{
           $req = $request->except('_token');
           $buyOrder = $req["buy_order"];

           $resp = (new MallTransaction)->status($buyOrder);

           return response(["req" => $req, "resp" => $resp],200);
       }catch (Exception $e) {
           return response(['error' => $e->getMessage()], 500);
       }
    }

    public function refund(Request $request)
    {
        try{
            $req = $request->except('_token');
            $buyOrder = $req["parent_buy_order"];
            $childCommerceCode = $req["commerce_code"];
            $childBuyOrder = $req["child_buy_order"];
            $amount = $req["amount"];

            $resp = (new MallTransaction)->refund($buyOrder, $childCommerceCode, $childBuyOrder, $amount);

            return response(["req" => $req, "resp" => $resp],200);
        }catch (Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteInscription(Request $request)
    {
       try{
           $req = $request->except('_token');
           $tbkUser = $req["tbk_user"];
           $userName = $req["user_name"];

           $resp = (new MallInscription)->delete($tbkUser, $userName);
           return response( ["req" => $req, "resp" => $resp],200);
       }catch (Exception $e) {
           return response(['error' => $e->getMessage()], 500);
       }
    }


}
