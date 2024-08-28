<?php

namespace App\Http\Controllers;

use App\Models\PreguntaFrecuentes;
use Illuminate\Http\Request;

class PreguntaFrecuentesController extends Controller
{

    public function getPreguntasFrecuentes()
    {
        try{
            $preguntas_frecuentes = PreguntaFrecuentes::orderBy('position','asc')->get();
            return response($preguntas_frecuentes,200);
        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }

     public function store(Request $request, $id=0){
            try{
                $request->validate([
                    'titulo' => 'required',
                    'descripcion' => 'required',
                ]);

                $preguntaFrecuente = PreguntaFrecuentes::findOrNew($id);
                $preguntaFrecuente->titulo = $request->titulo;
                $preguntaFrecuente->descripcion = $request->descripcion;
                $preguntaFrecuente->estado = $request->has('estado') ? $request->estado : 'enabled';
                $preguntaFrecuente->save();

                return response($preguntaFrecuente,200);

            }catch (\Exception $e){
                return response($e->getMessage(),200);
            }
    }

    public function destroy($id){
        try{
            $preguntaFrecuente = PreguntaFrecuentes::find($id);
            $preguntaFrecuente->delete();
            return response('Pregunta frecuente eliminada correctamente',200);
        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }

    public function ordenar(Request $request)
    {
        try {
            $sliders = json_decode($request->preguntas, true);
            foreach ($sliders as $index => $slider) {
                $slider = PreguntaFrecuentes::find($slider['id']);
                $slider->position = $index + 1;
                $slider->save();
            }
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }



}
