<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
//use App\Repositories\ClienteRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Image;
class ClienteController extends AppBaseController
{

    //generar estas 3 funciones getCliente store destroy
    public function getCliente(){
       try{
            $clientes = Cliente::orderBy('position','asc')
                            ->get();
            return response()->json($clientes);
        }catch(\Exception $e){
           return response($e->getMessage(),422);
        }
    }

    public function store(Request $request, $id = 0){
        try{
            $rules = [
                'nombre'=>'required',
                'imagen'=>'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'url'=>'required',
            ];

            if ($id == 0) { // If it's a new record
                $rules['imagen'] = 'required | ' . $rules['imagen'];
            }

            $request->validate($rules);

            if($request->hasFile('imagen')){
                $image = $request->file('imagen');
                $imagen = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/cliente');
                $image->move($destinationPath, $imagen);
            }

            $cliente = Cliente::findOrNew($id);
            $cliente->nombre = $request->nombre;
            $cliente->imagen =$request->has('imagen') ?  $imagen : $cliente->imagen;
            $cliente->url = $request->url;

            $cliente->status = $request->has('status') ? $request->status : 'enabled';

            $cliente->save();

            return response()->json($cliente);
        }catch (\Exception $e){
            return response($e->getMessage(),422);
        }
    }

    public function destroy($id){
        try{
            $cliente = Cliente::find($id);
            $cliente->delete();
            return response('Cliente Eliminado!',200);
        }catch (\Exception $e){
            return response($e->getMessage(),422);
        }
    }

    public function ordenar(Request $request)
    {
        try {
            $sliders = json_decode($request->clientes, true);
            foreach ($sliders as $index => $slider) {
                $slider = Cliente::find($slider['id']);
                $slider->position = $index + 1;
                $slider->save();
            }
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }

}
