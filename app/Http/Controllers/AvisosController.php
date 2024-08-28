<?php

namespace App\Http\Controllers;

use App\Models\Avisos;
use Illuminate\Http\Request;
use Flash;
use Image;
class AvisosController extends Controller
{
   public function getAvisos(){
       try{
           $avisos = Avisos::orderBy('id','desc')->paginate(15);
           return response($avisos,200);
       }catch (\Exception $e){
           return response($e->getMessage(),200);
       }
   }

   public function store(Request $request,$id=0){
       try{

           $rules = [
               'titulo' => 'required',
               'imagen' => $id == 0 ? 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' :
                   'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
           ];



           $request->validate($rules);

           $imagen = 'default.png';

           if ($id > 0) {
               $slider = Avisos::find($id);
               if ($slider) {
                   $imagen = $slider->imagen;
               }
           }

           if ($request->hasFile('imagen')) {
               $imageName = time() . '.' . $request->imagen->extension();
               $request->imagen->move(public_path('uploads/avisos'), $imageName);
               $imagen = $imageName;
           }

            $avisos = Avisos::findOrNew($id);
            $avisos->titulo = $request->titulo;
            $avisos->descripcion = $request->descripcion;
            $avisos->imagen = $imagen;

            $avisos->status = $request->has('status') ? $request->status : 'enabled';

            $avisos->save();

            return response($avisos,200);

       }catch (\Exception $e){
           return response($e->getMessage(),200);
       }
   }

    public function destroy($id){
         try{
              $avisos = Avisos::find($id);
              $avisos->delete();
              return response('Aviso eliminado correctamente',200);
         }catch (\Exception $e){
              return response($e->getMessage(),200);
         }
    }

}
