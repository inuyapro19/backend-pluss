<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDestinosRequest;
use App\Http\Requests\UpdateDestinosRequest;
use App\Models\Destinos;

//use App\Repositories\DestinosRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Lugar;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Str;
use Image;

class DestinosController extends AppBaseController
{

    //function  get destinos store y destroy con try catch y retornen un json

    /**
     * Display a listing of the Destinos.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $destinos = Destinos::orderBy('position', 'asc')->get();
            return response()->json($destinos);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function store(Request $request, $id = 0)
    {
        try {


            $rules = [
                'nombre_ciudad' => 'required',
                'descripcion' => 'required',
                'imagen' => $id == 0 ? 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' :
                'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'imagen_destino' => 'image | mimes:jpeg,png,jpg,gif | max:2048',
            ];

            if ($id == 0) { // If it's a new record
                $rules['imagen'] = 'required | ' . $rules['imagen'];
                $rules['imagen_destino'] = 'required | ' . $rules['imagen_destino'];
            }

            $request->validate($rules);


            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');
                $name = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/destinos/');

                $resize_image = Image::make($file->getRealPath());

                $resize_image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath . '/' . $name);

                $imagen = $name;
            }

            if ($request->hasFile('imagen_destino')) {
                $file = $request->file('imagen_destino');
                $name = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/destinos/');

                $resize_image = Image::make($file->getRealPath());

                $resize_image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath . '/' . $name);

                $imagen_destino = $name;
            }

            $destinos = Destinos::firstOrNew(['id' => $id]);
            $destinos->nombre_ciudad = $request->nombre_ciudad;
            $destinos->slug = Str::slug($request->nombre_ciudad);
            $destinos->descripcion = $request->descripcion;

            $destinos->imagen_destino = $request->hasFile('imagen_destino') ? $imagen_destino : $destinos->imagen_destino;
            $destinos->imagen = $request->hasFile('imagen') ? $imagen : $destinos->imagen;
            $destinos->texto_imagen = $request->texto_imagen;

            $destinos->status = $request->has('status') ? $request->status : 'enabled';
            $destinos->save();

            return response()->json($destinos);

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $destinos = Destinos::find($id);
            $destinos->delete();
            return response('Eliminado', 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
    public function ordenar(Request $request)
    {
        try {
            $sliders = json_decode($request->destinos, true);
            foreach ($sliders as $index => $slider) {
                $slider = Destinos::find($slider['id']);
                $slider->position = $index + 1;
                $slider->save();
            }
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }

}
