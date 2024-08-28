<?php

use App\Http\Controllers\AcomodacionController;
use App\Http\Controllers\AdminMenuController;
use App\Http\Controllers\AvisosController;
use App\Http\Controllers\BusesController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DestinosController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\OficinaController;
use App\Http\Controllers\PaginaController;
use App\Http\Controllers\PreguntaFrecuentesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SistemaController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Ventas\ApiRecorrido;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Ventas\CartTravelController;
use App\Http\Controllers\Ventas\CountryController;
use App\Http\Controllers\Ventas\OrderController;
use App\Http\Controllers\Ventas\PagoController;
use App\Http\Controllers\Ventas\PagoOnclickController;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\Ventas\PaymentTravelController;
use App\Http\Controllers\Ventas\SalesChartController;
use App\Http\Controllers\Ventas\SearchTravelController;
use App\Http\Controllers\Ventas\SignAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

//importa archivo de rutas
//require __DIR__.'/ventas.php';

Route::group(['prefix'=>'v1'],function(){
   // Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/getSettings', [PrincipalController::class, 'index']);

    Route::get('/getpagina/{slug}', [PrincipalController::class, 'pagina']);

    Route::get('/getnuestrosbuses', [PrincipalController::class, 'buses']);

    Route::get('/getacomodaciones', [PrincipalController::class, 'acomodaciones']);

    Route::get('getoficinas',[PrincipalController::class, 'oficinas']);

    Route::get('/getpuntopluss',[PrincipalController::class, 'punto_pluss']);

    Route::get('/getservicios/{slug}',[PrincipalController::class, 'sistemas']);

    Route::get('/getdestinos',[PrincipalController::class, 'destinos']);

    Route::get('getdestino/{id}',[PrincipalController::class, 'destinos_single']);

    Route::get('getclientes',[PrincipalController::class, 'cliente']);

   // Route::get('contacto', [PrincipalController::class, 'contacto']);

    Route::post('/enviarformulario', [PrincipalController::class, 'enviar_formulario']);

    //Route::get('compra-pasaje-plussChile',[PrincipalController::class, 'compra_pasaje']);

    Route::get('/getavisos',[PrincipalController::class, 'avisos_importantes']);

    Route::get('/preguntasfrecuentes',[PrincipalController::class, 'preguntas_frecuentes']);

    //Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

    //ruta para la api de recorrido y webpay
    Route::post('/crear_pago_orden', [PagoController::class,'createdTransaction']);

    Route::get('/pago/returnUrl', [PagoController::class,'commitTransaction']);
    Route::post('/pago/returnUrl', [PagoController::class,'cancelado']);

    //Route::get('/webpayplus/refund',  [\App\Http\Controllers\PagoController::class,'showRefund']);
    Route::post('/webpayplus/refund',  [PagoController::class,'refundTransaction']);

    Route::get('/webpayplus/transactionStatus',  [PagoController::class,'showGetStatus']);
    Route::post('/webpayplus/transactionStatus', [PagoController::class,'getTransactionStatus']);

    Route::get('/compra-cancelada',[PagoController::class,'cancelado_vista'])->name('pago.cancelado');

    Route::post('/create-order',[OrderController::class,'store']);


    //orders
    Route::get('/orders/{id?}', [OrderController::class,'index']);

    //users
    //Route::get('/getUser', [\App\Http\Controllers\Auth\LoginController::class,'getUsers']);

    # Oneclick Mall
    Route::get('/oneclick/startInscription', function() {
        return view('oneclick/start_inscription');
    });

    Route::post('/oneclick/startInscription', [PagoOnclickController::class, 'startInscription']);

    Route::delete('/oneclick/inscription', [PagoOnclickController::class, 'deleteInscription']);
    Route::get('/oneclick/inscription', [PagoOnclickController::class, 'getInscription']);

    Route::any('/oneclick/responseUrl', [PagoOnclickController::class, 'responseUrl']);

    Route::get('/oneclick/mall/authorizeTransaction', function () {

        return view('/oneclick/authorize_mall');

    });
    Route::post('/oneclick/mall/authorizeTransaction', [PagoOnclickController::class, 'authorizeTransaction']);

    Route::post('/oneclick/mall/transactionStatus', [PagoOnclickController::class, 'transactionStatus']);

    Route::post('/oneclick/mall/refund', [PagoOnclickController::class, 'refund']);

    Route::post('/oneclick/mall/increaseAmount', [PagoOnclickController::class, 'increaseAmount']);
    Route::post('/oneclick/mall/reverseAmount', [PagoOnclickController::class, 'reverseAmount']);
    Route::post('/oneclick/mall/increaseDate', [PagoOnclickController::class, 'increaseDate']);
    Route::post('/oneclick/mall/transactionHistory', [PagoOnclickController::class, 'transactionHistory']);


    //CMS - PAGINAS
    //Route::post('/settings/avisos', [SettingController::class, 'settingAvisos']);

    Route::get('/slider', [SliderController::class, 'getSlider']);
    Route::post('/slider/{id?}', [SliderController::class, 'store']);
    Route::delete('/slider/{id}', [SliderController::class, 'destroy']);
    Route::post('/slider-ordenar', [SliderController::class, 'ordenar']);

    //acomodaciones
    Route::get('/acomodaciones', [AcomodacionController::class, 'getAcomodacion']);
    Route::post('/acomodaciones/{id?}', [AcomodacionController::class, 'store']);
    Route::delete('/acomodaciones/{id}', [AcomodacionController::class, 'destroy']);
    //ordenar acomodaciones
    Route::post('/acomodaciones-ordenar', [AcomodacionController::class, 'ordenar']);

    //avisos
    Route::get('/avisos', [AvisosController::class, 'getAvisos']);
    Route::post('/avisos/{id?}', [AvisosController::class, 'store']);
    Route::delete('/avisos/{id}', [AvisosController::class, 'destroy']);

    //buses
    Route::get('/buses', [BusesController::class, 'getBuses']);
    Route::post('/buses/{id?}', [BusesController::class, 'store']);
    Route::delete('/buses/{id}', [BusesController::class, 'destroy']);
    //ordenar buses
    Route::post('/buses-ordenar', [BusesController::class, 'ordenar']);

    //clientes
    Route::get('/clientes', [ClienteController::class, 'getCliente']);
    Route::post('/clientes/{id?}', [ClienteController::class, 'store']);
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);
    //ordenar clientes
    Route::post('/clientes-ordenar', [ClienteController::class, 'ordenar']);

    //destinos
    Route::get('/destinos', [DestinosController::class, 'index']);
    Route::post('/destinos/{id?}', [DestinosController::class, 'store']);
    Route::delete('/destinos/{id}', [DestinosController::class, 'destroy']);
    //ordenar destinos
    Route::post('/destinos-ordenar', [DestinosController::class, 'ordenar']);
    //PreguntaFrecuentesController
    Route::get('/preguntasfrecuentes', [PreguntaFrecuentesController::class, 'getPreguntasFrecuentes']);
    Route::post('/preguntasfrecuentes/{id?}', [PreguntaFrecuentesController::class, 'store']);
    Route::delete('/preguntasfrecuentes/{id}', [PreguntaFrecuentesController::class, 'destroy']);
    //ordenar preguntas frecuentes
    Route::post('/preguntas-ordenar', [PreguntaFrecuentesController::class, 'ordenar']);

    //PaginaController
    Route::get('/paginas', [PaginaController::class, 'index']);
    Route::post('/paginas/{id?}', [PaginaController::class, 'store']);
    Route::delete('/paginas/{id}', [PaginaController::class, 'destroy']);

    //SettingController
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'store']);

    //OficinaController
    Route::get('/oficinas', [OficinaController::class, 'index']);
    Route::post('/oficinas/{id?}', [OficinaController::class, 'store']);
    Route::delete('/oficinas/{id}', [OficinaController::class, 'destroy']);
    //ordenar oficinas
    Route::post('/oficinas-ordenar', [OficinaController::class, 'ordenar']);

    //UsuarioController
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios/{id?}', [UsuarioController::class, 'store']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

    //LugarController
    Route::get('/lugares', [LugarController::class, 'index']);
    Route::post('/lugares/{id?}', [LugarController::class, 'store']);
    Route::delete('/lugares/{id}', [LugarController::class, 'destroy']);

    //SistemaController
    Route::get('/sistemas', [SistemaController::class, 'index']);
    Route::post('/sistemas/{id?}', [SistemaController::class, 'store']);
    Route::delete('/sistemas/{id}', [SistemaController::class, 'destroy']);
    //ordenar sistemas
    Route::post('/sistemas-ordenar', [SistemaController::class, 'ordenar']);

    //AdminMenuController
    Route::get('/adminmenu/{id?}', [AdminMenuController::class, 'index']);
    Route::post('/adminmenu', [AdminMenuController::class, 'store']);
    Route::post('/adminmenu-item/{id?}', [AdminMenuController::class, 'storeMenuItem']);
    Route::delete('/adminmenu/{id?}', [AdminMenuController::class, 'destroy']);
    Route::post('/adminmenu-item-ordenar', [AdminMenuController::class, 'orderMenuitem']);

    //OrderController
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/sales-data', [SalesChartController::class, 'getMonthlySalesData']);
    Route::get('/sales-month', [SalesChartController::class, 'getDailySalesData']);

    //SignAuthController
    Route::get('/signInAgency', [SignAuthController::class, 'signInAgency']);
    Route::get('/getAgenciaCookie', [SignAuthController::class, 'getAgenciaCookie']);

    //SearchTravelController
    Route::get('/getCities', [SearchTravelController::class, 'getCities']);
    Route::post('/getBusTravel', [SearchTravelController::class, 'getBusTravel']);
    Route::post('/getBusTravelSearchResults', [SearchTravelController::class, 'getBusTravelSearchResults']);

    //CartTravelController
    Route::post('/getBooking', [CartTravelController::class, 'getBooking']);
    Route::post('/getBookingPassengers', [CartTravelController::class, 'getBookingPassengers']);
    Route::post('/getBookingSeats', [CartTravelController::class, 'getBookingSeats']);
    Route::post('/getBookingPassengersPost', [CartTravelController::class, 'getBookingPassengersPost']);

    // PaymentTravelController
    Route::post('/getBookingBook', [PaymentTravelController::class, 'getBookingBook']);
    Route::post('/getTicketPdf', [PaymentTravelController::class, 'getBookingPdf']);

    //obtienes los datos almacenasdos en la base de datos para el tarjeta de credito
    Route::post('/getCreditCardData',[OrderController::class,'getCreditCardData']);

    //countries
    Route::get('/countries', [CountryController::class,'index']);

  /*  //api recorrido
    Route::get('/signInAgency', [ApiRecorrido::class,'signInAgency']);
    //getAuthorizationUrl
    Route::get('/getAuthorizationUrl', [ApiRecorrido::class,'getAuthorizationUrl']);
    //getAccessToken
    Route::post('/getAccessToken', [ApiRecorrido::class,'getAccessToken']);
    //get cities
    Route::get('/getCities',[ApiRecorrido::class,'getCities']);
    //getBusTravel
    Route::post('/getBusTravel',[ApiRecorrido::class,'getBusTravel']);
    //getBusTravelSearchResults
    Route::post('/getBusTravelSearchResults',[ApiRecorrido::class,'getBusTravelSearchResults']);
    //getBooking
    Route::post('/getBooking',[ApiRecorrido::class,'getBooking']);
    //getBookingPassengers
    Route::post('/getBookingPassengers',[ApiRecorrido::class,'getBookingPassengers']);
    //getBookingSeats
    Route::post('/getBookingSeats',[ApiRecorrido::class,'getBookingSeats']);
    //getBookingPassengersPost
    Route::post('/getBookingPassengersPost',[ApiRecorrido::class,'getBookingPassengersPost']);
    //getBookingBook
    Route::post('/getBookingBook',[ApiRecorrido::class,'getBookingBook']);

    //getBookingPdf
    Route::post('/getTicketPdf',[ApiRecorrido::class,'getBookingPdf']);

    //obtienes los datos almacenasdos en la base de datos para el tarjeta de credito
    Route::post('/getCreditCardData',[OrderController::class,'getCreditCardData']);

    //countries
    Route::get('/countries', [CountryController::class,'index']);*/
});
