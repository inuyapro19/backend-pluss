<?php

namespace App\Http\Controllers\Ventas;


use App\Http\Controllers\Controller;
use App\Models\Ventas\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    //

    public function index()
    {
        try{

            $countries = Country::orderBy('name', 'asc')
                        ->select('code','name')
                        ->get();
            return response($countries, 200);

        }catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

    }

}
