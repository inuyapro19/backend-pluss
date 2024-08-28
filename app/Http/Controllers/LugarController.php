<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreateLugarRequest;
use App\Http\Requests\UpdateLugarRequest;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LugarController extends Controller
{
    public function index(Request $request)
    {
        try{
            $lugares = Lugar::all();
            return response()->json($lugares);
        }catch (\Exception $e){
            return response()->json($e->getMessage());
        }
    }

    public function store(Request $request, $id = 0)
    {
        try{

            $rules = [
                'destinos_id' => 'required',
                'nombre_lugar' => 'required',
                /*'imagen' => $id == 0 ? 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' :
                    'image|mimes:jpeg,png,jpg,gif,svg|max:2048',*/
            ];



            $request->validate($rules);

           $imagen = 'default.png';

            if ($id > 0) {
                $slider = Lugar::find($id);
                if ($slider) {
                    $imagen = $slider->imagen;
                }
            }

            if ($request->hasFile('imagen')) {
                $imageName = time() . '.' . $request->imagen->extension();
                $request->imagen->move(public_path('/uploads/lugares/'), $imageName);
                $imagen = $imageName;
            }


            $lugar = Lugar::firstOrNew(['id' => $id]);
            $lugar->destinos_id = $request->destinos_id;
            $lugar->nombre_lugar = $request->nombre_lugar;
            $lugar->descripcion = $request->descripcion;
            $lugar->texto_imagen = $request->texto_imagen;
            $lugar->imagen = $imagen;
            $lugar->status = $request->has('status') ? $request->status : 'enabled';
            $lugar->save();

            return response()->json($lugar);

        }catch (\Exception $e){
            return response()->json($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $lugar = Lugar::find($id);
            $lugar->delete();
            return response('Eliminado',200);
        }catch (\Exception $e){
            return response()->json($e->getMessage());
        }
    }
}
