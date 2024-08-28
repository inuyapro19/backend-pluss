<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{

    public function index()
    {
        try{
            $usuarios = User::all();
            return response()->json($usuarios);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Error a listar usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $id = 0)
    {
        try {
            // Reglas de validación básicas
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
            ];

            // Añadir la regla de contraseña solo si se está creando un nuevo usuario
            // o si se proporcionó una nueva contraseña en la solicitud
            if ($id == 0 || $request->has('password')) {
                $rules['password'] = 'required|min:6';
            }

            // Validación de la solicitud
            $request->validate($rules);

            // Crear o actualizar usuario
            $usuario = User::findOrNew($id);
            $usuario->name = $request->name;
            $usuario->email = $request->email;

            // Actualizar la contraseña solo si se proporcionó una nueva
            if ($request->has('password')) {
                $usuario->password = Hash::make($request->password);
            }

            $usuario->save();

            return response()->json([
                'message' => 'Usuario creado o actualizado con éxito',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id){
        try{
            $usuario = User::find($id);
            $usuario->delete();
            return response()->json([
                'message' => 'Usuario eliminado con éxito',
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
