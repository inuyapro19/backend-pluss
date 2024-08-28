<?php


use App\Http\Controllers\PagoOnclickController;

Route::group(['prefix' => 'v1'], function() {
    Route::post('/crear_pago_orden', [\App\Http\Controllers\PagoController::class,'createdTransaction']);

    Route::get('/pago/returnUrl', [\App\Http\Controllers\PagoController::class,'commitTransaction']);
    Route::post('/pago/returnUrl', [\App\Http\Controllers\PagoController::class,'cancelado']);

    //Route::get('/webpayplus/refund',  [\App\Http\Controllers\PagoController::class,'showRefund']);
    Route::post('/webpayplus/refund',  [\App\Http\Controllers\PagoController::class,'refundTransaction']);

    Route::get('/webpayplus/transactionStatus',  [\App\Http\Controllers\PagoController::class,'showGetStatus']);
    Route::post('/webpayplus/transactionStatus', [\App\Http\Controllers\PagoController::class,'getTransactionStatus']);

    Route::get('/compra-cancelada',[\App\Http\Controllers\PagoController::class,'cancelado_vista'])->name('pago.cancelado');

    Route::post('/create-order',[\App\Http\Controllers\OrderController::class,'store']);
    //api recorrido
    Route::get('/signInAgency', [\App\Http\Controllers\ApiRecorrido::class,'signInAgency']);
    //getAuthorizationUrl
    Route::get('/getAuthorizationUrl', [\App\Http\Controllers\ApiRecorrido::class,'getAuthorizationUrl']);
    //getAccessToken
    Route::post('/getAccessToken', [\App\Http\Controllers\ApiRecorrido::class,'getAccessToken']);
    //get cities
    Route::get('/getCities',[\App\Http\Controllers\ApiRecorrido::class,'getCities']);
    //getBusTravel
    Route::post('/getBusTravel',[\App\Http\Controllers\ApiRecorrido::class,'getBusTravel']);
    //getBusTravelSearchResults
    Route::post('/getBusTravelSearchResults',[\App\Http\Controllers\ApiRecorrido::class,'getBusTravelSearchResults']);
    //getBooking
    Route::post('/getBooking',[\App\Http\Controllers\ApiRecorrido::class,'getBooking']);
    //getBookingPassengers
    Route::post('/getBookingPassengers',[\App\Http\Controllers\ApiRecorrido::class,'getBookingPassengers']);
    //getBookingSeats
    Route::post('/getBookingSeats',[\App\Http\Controllers\ApiRecorrido::class,'getBookingSeats']);
    //getBookingPassengersPost
    Route::post('/getBookingPassengersPost',[\App\Http\Controllers\ApiRecorrido::class,'getBookingPassengersPost']);
    //getBookingBook
    Route::post('/getBookingBook',[\App\Http\Controllers\ApiRecorrido::class,'getBookingBook']);

    //getBookingPdf
    Route::post('/getTicketPdf',[\App\Http\Controllers\ApiRecorrido::class,'getBookingPdf']);

    //obtienes los datos almacenasdos en la base de datos para el tarjeta de credito
    Route::post('/getCreditCardData',[\App\Http\Controllers\OrderController::class,'getCreditCardData']);

    //countries
    Route::get('/countries', [\App\Http\Controllers\CountryController::class,'index']);

    //orders
    Route::get('/orders/{id?}', [\App\Http\Controllers\OrderController::class,'index']);

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


});
