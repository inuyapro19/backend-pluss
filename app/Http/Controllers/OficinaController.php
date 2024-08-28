<?php

namespace App\Http\Controllers;

use App\Models\Oficina;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class OficinaController extends Controller
{
    public function index(){
        try{
            $oficinas = Oficina::all();
            return response($oficinas,200);
        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }

    public function store(Request $request, $id=0){
            try{


                $request->validate([
                    'ciudad' => 'required',
                    'tipo' => 'required',
                    'direccion' => 'required',
                    'telefono' => 'required',
                    'horario_at' => 'required',
                    'mapa' => 'required',
                ]);


                $imagen = 'default.png';

                if ($id > 0) {
                    $slider = Oficina::find($id);
                    if ($slider) {
                        $imagen = $slider->imagen;
                    }
                }

                if ($request->hasFile('imagen')) {
                    $imageName = time() . '.' . $request->imagen->extension();
                    $request->imagen->move(public_path('/uploads/oficinas/'), $imageName);
                    $imagen = $imageName;
                }

                $oficina = Oficina::findOrNew($id);
                $oficina->ciudad = $request->ciudad;
                $oficina->tipo = $request->tipo;
                $oficina->direccion = $request->direccion;
                $oficina->telefono = $request->telefono;
                $oficina->horario_at = $request->horario_at;
                $oficina->mapa = $request->mapa;

                $oficina->imagen = $request->has('imagen') ? $imagen : $oficina->imagen;

                $oficina->status = $request->has('status') ? $request->status : 'enabled';

                $oficina->save();

                return response($oficina,200);

            }catch (\Exception $e){
                return response($e->getMessage(),200);
            }
    }

    public function destroy($id){
        try{
            $oficina = Oficina::find($id);
            $oficina->delete();
            return response('Oficina eliminada correctamente',200);
        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }

    public function ordenar(Request $request)
    {
        try {
            $sliders = json_decode($request->oficinas, true);
            foreach ($sliders as $index => $slider) {
                $slider = Oficina::find($slider['id']);
                $slider->position = $index + 1;
                $slider->save();
            }
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }

}
