<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAcomodacionRequest;
use App\Http\Requests\UpdateAcomodacionRequest;
use App\Models\Acomodacion;
//use App\Repositories\AcomodacionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Image;

class AcomodacionController extends AppBaseController
{

    public function getAcomodacion()
    {
        try{
            $acomodaciones = Acomodacion::orderBy('position', 'asc')
                ->get();
            return response($acomodaciones,200);
        }catch (\Exception $e){
            $acomodaciones = [];
            return response($e,404);
        }
    }


    public function store(Request $request, $id=0){
        try{

            $rules = [
                'titulo' => 'required',
                'descripcion' => 'required',
            ];



            $request->validate($rules);

            if($request->hasFile('imagen')){
                $imageName = time().'.'.$request->imagen->extension();
                $request->imagen->move(public_path('uploads/aco'), $imageName);
                $imagen = $imageName;
            }else{
                $imagen = 'default.png';
            }

            $acomodacion = Acomodacion::findOrNew($id);
            $acomodacion->titulo = $request->titulo;
            $acomodacion->descripcion = $request->descripcion;
            $acomodacion->imagen = $request->has('imagen') ? $imagen : $acomodacion->imagen;
            $acomodacion->status = $request->has('status') ? $request->status : 'enabled';
            $acomodacion->save();

            return response($acomodacion,200);

        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }

    public function destroy($id){
        try{
            $acomodacion = Acomodacion::find($id);
            $acomodacion->delete();
            return response('Acomodacion eliminada correctamente',200);
        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }
    public function ordenar(Request $request)
    {
        try {
            $sliders = json_decode($request->acomodaciones, true);
            foreach ($sliders as $index => $slider) {
                $slider = Acomodacion::find($slider['id']);
                $slider->position = $index + 1;
                $slider->save();
            }
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }
}
