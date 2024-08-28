<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\AgenciaCookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
class SignAuthController extends Controller
{
    public function signInAgency(){
        try {
            $response = Http::withBasicAuth('plusschile', 'plusschile')
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(10)
                ->post(env('API_RECORRIDO_URL').'/api/v2/es/agencies/sign_in.json', [
                    "agency" => [
                        "email" => env('API_AGENCY_USERNAME'),
                        "password" => env('API_AGENCY_PASSWORD')
                    ]
                ]);

            $cookie = $response->cookies()->getCookieByName('_recorrido_session');
            Cache::put('_recorrido_session', $cookie->getValue(), now()->addMinutes(30)); // Ajusta el tiempo según sea necesario
            Cache::put('_recorrido_session_domain', $cookie->getDomain(), now()->addMinutes(30)); // Ajusta el tiempo según sea necesario
            $dominio = $cookie->getDomain();
            $data = $this->getAuthorizationUrl($dominio);
            $data2 = json_decode($data->getContent(), true);
            $code = $data2['code'];

            $resToken = $this->getAccessToken($dominio, $code);
            $token = json_decode($resToken->getContent(), true);

            // Aquí podrías hacer algo con el token, si es necesario

            Log::channel('file')->info('Se creo la session de la api', [
                'PASO' => '1',
                'information' => $resToken->getContent(),
            ]);

            return response($resToken->getContent(), 200);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    private function getAuthorizationUrl($dominio){
        if (Cache::has('_recorrido_session')) {
            $array_cookies = ['_recorrido_session' => Cache::get('_recorrido_session')];

            try {
                $response = Http::withBasicAuth('plusschile', 'plusschile')
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->withCookies($array_cookies, $dominio)
                    ->timeout(10)
                    ->post(env('API_RECORRIDO_URL').'/oauth/authorize.json', [
                        "client_id" => env('API_CLIENT_ID'),
                        "redirect_uri" => env('API_RECORRIDO_URL'),
                        "response_type" => "code"
                    ]);

                Log::channel('file')->info('Se creo el codigo de acceso de la api', [
                    'PASO' => '2',
                    'information' => $response->getBody(),
                ]);

                return response($response->getBody(), 200);

            } catch (\Exception $exception) {
                return response()->json([
                    'error' => $exception->getMessage()
                ], 500);
            }
        } else {
            // Manejar el caso de que la cookie no esté en el caché
            return response()->json([
                'error' => 'No se encontró la cookie en el caché'
            ], 500);
        }
    }

    private function getAccessToken($dominio, $code){
        if (Cache::has('_recorrido_session')) {
            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(10)
                    ->post(env('API_RECORRIDO_URL').'/oauth/token.json', [
                        "code" => $code,
                        "client_id" => env('API_CLIENT_ID'),
                        "client_secret" => env('API_CLIENT_SECRET'),
                        'redirect_uri' => env('API_RECORRIDO_URL'),
                        "grant_type" => "authorization_code"
                    ]);

                Log::channel('file')->info('Se creo el token de acceso de la api', [
                    'PASO' => '3',
                    'information' => $response->getBody(),
                ]);

                return response($response->getBody(), 200);

            } catch (\Exception $exception) {
                return response()->json([
                    'error' => $exception->getMessage()
                ], 500);
            }
        } else {
            // Manejar el caso de que la cookie no esté en el caché

            return response()->json([
                'error' => 'No se encontró la cookie en el caché'
            ], 500);
        }
    }

    public function getAgenciaCookie(){
        if (Cache::has('_recorrido_session')) {
            // La cookie está en el caché
            $cookieValue = Cache::get('_recorrido_session');
            $cookieValueDomain = Cache::get('_recorrido_session_domain');
            // Aquí puedes realizar las operaciones que necesites con la cookie
            return response()->json([
                'cookie' => $cookieValue,
                'domain' => $cookieValueDomain
            ], 200);
        } else {
            return response()->json([
                'error' => 'No se encontró la cookie en el caché'
            ], 500);
        }
    }


}
