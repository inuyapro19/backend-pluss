<?php

namespace App\Http\Controllers;

use App\Models\Pagina;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Image;



class PaginaController extends Controller
{
    public function index(){
        try{
            $paginas = Pagina::all();
            return response($paginas,200);
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

                if ($request->hasFile('imagen')){
                    $imagen = $request->file('imagen');
                    $nombre_imagen = time().'_'.$imagen->getClientOriginalName();
                    $ruta_imagen = public_path().'/uploads/paginas/'.$nombre_imagen;
                    Image::make($imagen->getRealPath())->save($ruta_imagen);
                }

                $pagina = Pagina::findOrNew($id);
                $pagina->titulo = $request->titulo;

                if ($id==0){
                    $pagina->slug = Str::slug($request->titulo);
                }

                if ($request->hasFile('imagen')){
                    $pagina->imagen = $nombre_imagen;
                }

                $pagina->descripcion = $request->descripcion;
                $pagina->save();

                return response($pagina,200);

            }catch (\Exception $e){
                return response($e->getMessage(),200);
            }
    }

    public function destroy($id){
        try{
            $pagina = Pagina::find($id);
            $pagina->delete();
            return response('Pagina eliminada correctamente',200);
        }catch (\Exception $e){
            return response($e->getMessage(),200);
        }
    }

}
