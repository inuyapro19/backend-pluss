<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


class SistemaController extends Controller
{
    public function index(){
        try{
            $sistemas = Sistema::all();
            return response($sistemas,200);
        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }

    public function store(Request $request, $id=0){
            try{
                $request->validate([
                    'categoria' => 'required',
                    'titulo' => 'required',
                    'descripcion' => 'required',
                    'imagen' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);


                $imagen = 'default.png';

                if ($id > 0) {
                    $slider = Sistema::find($id);
                    if ($slider) {
                        $imagen = $slider->imagen;
                    }
                }

                if ($request->hasFile('imagen')) {
                    $imageName = time() . '.' . $request->imagen->extension();
                    $request->imagen->move(public_path('/uploads/servicios/'), $imageName);
                    $imagen = $imageName;
                }

                $sistema = Sistema::findOrNew($id);
                $sistema->categoria = $request->categoria;
                $sistema->titulo = $request->titulo;
                $sistema->descripcion = $request->descripcion;

                $sistema->imagen = $request->has('imagen') ? $imagen : $sistema->imagen;

                $sistema->status = $request->has('status') ? $request->status : 'enabled';

                $sistema->save();

                return response($sistema,200);

            }catch (\Exception $e){
                return response($e->getMessage(),200);
            }
    }

    public function destroy($id){
        try{
            $sistema = Sistema::find($id);
            $sistema->delete();
            return response('Sistema eliminado correctamente',200);
        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }
    public function ordenar(Request $request)
    {
        try {
            $sliders = json_decode($request->servicios, true);
            foreach ($sliders as $index => $slider) {
                $slider = Sistema::find($slider['id']);
                $slider->position = $index + 1;
                $slider->save();
            }
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }
}
