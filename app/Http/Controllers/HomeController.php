<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contacto;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacto = Contacto::orderBy('id','desc')->limit(10)->get();

        return view('dashboard',['contacto'=>$contacto]);
    }
}
