<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBusesRequest;
use App\Http\Requests\UpdateBusesRequest;
use App\Models\Buses;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Image;
class BusesController extends AppBaseController
{
   public function getBuses()
   {
         try{
              $buses = Buses::orderBy('position','asc')->get();
              return response($buses,200);
         }catch (\Exception $e){
              return response($e->getMessage(),200);
         }
   }

   public function store(Request $request, $id=0){
       try{

           $rules = [
               'titulo' => 'required',
               'modelo'=>'required',
               'descripcion'=>'required',
               'imagen' => $id == 0 ? 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' :
                   'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
           ];



           $request->validate($rules);
           $imagen = 'default.png';
           if ($id > 0) {
               $slider = Buses::find($id);
               if ($slider) {
                   $imagen = $slider->imagen;
               }
           }

           if ($request->hasFile('imagen')) {
               $imageName = time() . '.' . $request->imagen->extension();
               $request->imagen->move(public_path('uploads/buses'), $imageName);
               $imagen = $imageName;
           }

                $buses = Buses::findOrNew($id);
                $buses->titulo = $request->titulo;
                $buses->modelo = $request->modelo;
                $buses->descripcion = $request->descripcion;
                $buses->imagen = $imagen;

                $buses->status = $request->has('status') ? $request->status : 'enabled';

                $buses->save();

                return response($buses,200);

       }catch (\Exception $e){
           return response($e->getMessage(),200);
       }
   }

   public function destroy($id){
        try{
             $buses = Buses::find($id);
             $buses->delete();
             return response('Bus eliminado correctamente',200);
        }catch (\Exception $e){
             return response($e->getMessage(),200);
        }
   }

    public function ordenar(Request $request)
    {
        try {
            $buses = json_decode($request->buses, true);
            foreach ($buses as $index => $bus) {
                $buse = Buses::find($bus['id']);
                $buse->position = $index + 1;
                $buse->save();
            }
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }
}
