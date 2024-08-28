<?php

namespace App\Http\Controllers;

use App\Mail\ClienteMail;
use App\Mail\ContactoMail;

use App\Models\AdminMenu;
use App\Models\Avisos;
use App\Models\Configuraciones;
use App\Models\Contacto;
use App\Models\Pagina;
use App\Models\Setting;
use App\Models\Sistema;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Oficina;

use App\Models\Acomodacion;
use App\Models\Buses;
use App\Models\Destinos;
use App\Models\Cliente;

use App\Models\PreguntaFrecuentes;
use Illuminate\Support\Facades\Mail;

class PrincipalController extends Controller
{

    private $oficinas;

    public function __construct(Oficina $oficina)
    {
        $this->oficinas=$oficina->oficinas_menu();
    }


    public function index(Pagina $pagina, Oficina $oficinas, Slider $slider, Destinos $destinos, Cliente $cliente, Configuraciones $configuraciones)
    {
        try {
            $responseData = [
                'menu' => $this->getMenuItems(),
                'slider' => $this->getSliderData($slider, 'slider-grander'),
                'seguridad' => $this->getSliderData($slider, 'slider-seguridad'),
                'bioseguridad' => $this->getSliderData($slider, 'slider-bioseguridad'),
                'entrencion' => $this->getSliderData($slider, 'slider-pequeno'),
                'destinos' => $destinos->where('status','enabled')->orderBy('position', 'asc')->get(),
                'clientes' => $cliente->where('status','enabled')->orderBy('position', 'asc')->get(),
                'settings' => $configuraciones->select('key','value')->get(),
                'settinsAll'=>$this->getSettingAll(),
            ];

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getMenuItems()
    {
       try{

           return AdminMenu::with('items')->get();
       }catch (\Exception $e) {
           return response()->json(['error' => $e->getMessage()], 500);
       }
    }

    private function getSliderData(Slider $slider, $category)
    {
        return $slider->where('categorias', '=', $category)
            ->where('status', '=', 'enabled')
            ->orderBy('position', 'asc')
            ->get();
    }

    private function getSettingAll()
    {
        //$locale = \request('locale');
        // Obtener todas las configuraciones
        $settings = Setting::get();

        // Crear un array para almacenar los datos saneados
        $sanitizedSettings = [];

        // Iterar sobre cada configuración
        foreach ($settings as $setting) {
            // Obtener el array de la configuración
            $settingArray = $setting->toArray();

            // Sanear el valor de la clave y asignarlo al array
            $sanitizedSettings[$settingArray['key']] = filter_var($settingArray['value'], FILTER_SANITIZE_STRING);
        }

        // Devolver una respuesta JSON con todas las configuraciones saneadas
        return $sanitizedSettings;
    }


    public function pagina($slug,Pagina $pagina,Oficina $oficinas)
    {

        try{
            $paginas = Pagina::where('slug','=',$slug)->first();
            return response()->json(['paginas' => $paginas],200);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()],500);
        }

    }

    public function buses()
    {
        try {
            $buses = Buses::where('status','enabled')->orderBy('position', 'asc')->get();
            $responseData = [
                'buses' => $buses
            ];
            return response($responseData, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }


    public function acomodaciones()
    {
        try {
            $aco = Acomodacion::where('status','enabled')
                ->orderBy('position', 'asc')
                ->get();
            $responseData = [
                'aco' => $aco
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function oficinas()
    {
        try {
            $oficinas = Oficina::where('tipo', '=', 'oficina')
                ->where('status','enabled')
                ->orderBy('position', 'asc')->get();
            $responseData = [
                'oficinas' => $oficinas
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function punto_pluss()
    {
        try {
            // Obtenemos las oficinas de tipo "punto-pluss" ordenadas por posición
            $oficinas = Oficina::where('tipo', '=', 'punto-pluss')
                ->where('status','enabled')
                ->orderBy('position', 'asc')
                ->get();

            // Creamos un arreglo con los datos de las oficinas
            $responseData = [
                'oficinas' => $oficinas
            ];

            // Retornamos una respuesta HTTP 200 con los datos en formato JSON
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            // Si ocurre alguna excepción, enviamos una respuesta HTTP 500 con un mensaje de error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destinos()
    {
        try {
            $destinos = Destinos::where('status','enabled')
                ->orderBy('position', 'asc')->get();

            $responseData = [
                'destinos' => $destinos,
            ];

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destinos_single($slug)
    {
        try {
            $destinos = Destinos::with(['lugares'])->where('slug', $slug)
                ->where('status','enabled')
                ->orderBy('id', 'asc')
                ->first();

            if (!$destinos) {
                return response(['message' => 'No se encontró el destino'], 404);
            }

            $responseData = [
                'destinos' => $destinos,
            ];

            return response($responseData, 200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }


    public function sistemas($slug)
    {
        try {
            $sistemas = Sistema::where('categoria', $slug)
                ->where('status','enabled')
                ->orderBy('position', 'asc')
                ->get();

            $responseData = [
                'sistemas' => $sistemas,
                'slug' => $slug,
            ];

            return response($responseData, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }


    public function cliente()
    {
        try {
            $clientes = Cliente::where('status','enabled')
                ->orderBy('position', 'asc')
                ->get();

            $responseData = [
                'cliente' => $clientes,
            ];

            return response($responseData, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }




    public function avisos_importantes()
    {
        try {
            $avisos = Avisos::where('status','enabled')
                ->orderBy('created_at', 'desc')->paginate(8);
            return response($avisos, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }

    public function preguntas_frecuentes()
    {
        try {
            $preguntas_frecuente = PreguntaFrecuentes::where('estado', 'enabled')
                ->orderBy('position', 'asc')->get();
            return response($preguntas_frecuente, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }


    public function enviar_formulario(Request $request)
    {
        try {
            // Validar el formulario
            $validatedData = $request->validate([
                'tipo_mensaje' => 'required',
                'rut' => 'required',
                'nombre' => 'required',
              /*  'patente_vehiculo' => 'required',
                'fecha_denuncia' => 'required',
                'lugar_denunciado' => 'required',
                'ciudad_origen' => 'required',
                'ciudad_destino' => 'required',*/
                'telefono' => 'required',
                'email' => 'required|email',
                'ciudad_residencia' => 'required',
                'mensaje' => 'required',
                //'g-recaptcha-response' => 'required'
            ]);


            // Verificar el captcha
           $captchaResponse = $this->verificarCaptcha($request->input('g-recaptcha-response'));

            if (!$captchaResponse->success) {
                return response(['error' => 'Captcha inválido'], 500);
            }

            // Crear un nuevo contacto
            $contacto = new Contacto();
            $contacto->tipo_mensaje = $request->tipo_mensaje;
            $contacto->rut = $request->rut;
            $contacto->nombre = $request->nombre;
            $contacto->patente_vehiculo = $request->patente_vehiculo ?? '';
            $contacto->fecha_denuncia = $request->fecha_denuncia ?? '2023-01-01';
            $contacto->lugar_denunciado = $request->lugar_denunciado ?? '';
            $contacto->ciudad_origen = $request->ciudad_origen ?? '';
            $contacto->ciudad_destino = $request->ciudad_destino ?? '';
            $contacto->telefono = $request->telefono;
            //$contacto->email = $request->email;
            $contacto->ciudad_residencia = $request->ciudad_residencia;
            $contacto->mensaje = $request->mensaje;
            $contacto->save();

            // Enviar correos electrónicos
            $motivo = $this->devolver_motivo($request->tipo_mensaje);

            Mail::to('info@plusschile.cl')->send(new ContactoMail(
                $motivo,
                $request->rut,
                $request->nombre,
                $request->patente_vehiculo,
                $request->fecha_denuncia,
                $request->lugar_denunciado,
                $request->ciudad_origen,
                $request->ciudad_destino,
                $request->telefono,
                $request->email,
                $request->ciudad_residencia,
                $request->mensaje
            ));
            Mail::to($request->email)->send(new ClienteMail(
                $motivo,
                $request->rut,
                $request->nombre,
                $request->patente_vehiculo,
                $request->fecha_denuncia,
                $request->lugar_denunciado,
                $request->ciudad_origen,
                $request->ciudad_destino,
                $request->telefono,
                $request->email,
                $request->ciudad_residencia,
                $request->mensaje
            ));

            // Redireccionar con un mensaje de éxito
            return response(['message' => 'Mensaje enviado correctamente'], 200);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()],500);
        }
    }

    public function verificarCaptcha($response)
    {
        $secret = '6Lcu48sZAAAAACkv-GiwsLBG01LJkuN00R1dZFVJ';
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$response);
        $responseData = json_decode($verifyResponse);

        return ($responseData->success) ? true : false;
    }
    public function devolver_motivo($motivo){
        $valor = '';
       if($motivo == 'cotizacion'){
           $valor = 'Cotización';
       }
        if($motivo == 'informacion'){
            $valor = 'Información';
        }

        if($motivo == 'sugerencia'){
            $valor = 'Sugerencia';
        }

        if($motivo == 'reclamo-denuncia'){
            $valor = 'Reclamo y/o Denuncia';
        }

        if($motivo == 'felicitaciones'){
            $valor = 'Felicitaciones';
        }
      return $valor;
    }


}
