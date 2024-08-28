<?php

namespace App\Http\Controllers\Ventas;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
class CitiesController extends Controller
{
    private $url;

    public function __construct()
    {
        $this->url = 'https://demo.recorrido.cl/api/v2/es';
    }

    public function getCities(){
       try {
           $credentials = base64_encode('plusschile:plusschile');
           $respuesta =  new Client();

           $respuesta->get('https://demo.recorrido.cl/api/v2/es/cities.json?country=chile', [
                   'headers' => [
                       'Authorization' => 'Basic ' . $credentials,
                       'x-filter-with-bo' => 229,
                   ]
           ]);
            ddd($respuesta);
          return response($respuesta,200);

       }catch (\Exception $exception){

            return response($exception,422);

       }
   }
}
