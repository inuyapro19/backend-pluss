<?php

namespace App\Http\Controllers;

use App\Models\AdminMenu;
use App\Models\AdminMenuItem;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    public function index($id = 0)
    {
        try {
            $tipo = \request()->get('tipo');
            if ($tipo == 'menu'){
                $menus = AdminMenu::all();
                return response()->json([
                    'status' => 'success',
                    'data' => $menus
                ]);
            }else{
                $items = AdminMenuItem::where('menu' ,$id)
                                        ->orderBy('sort','asc')
                                        ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $items
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->status(500);
        }
    }

    public function store(Request $request)
    {
        try {
            $menu = AdminMenu::create([
                'name' => $request->name
            ]);
            return response()->json([
                'status' => 'success',
                'data' => $menu
            ])->status(200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->status(500);
        }
    }


    public function storeMenuItem(Request $request, $id = 0){
        try {

            $request->validate([
                'label' => 'required',
                'url' => 'required',
                'menu' => 'required'
            ]);

            $menu = AdminMenuItem::findOrNew($id);

            $menu->label = $request->label;
            $menu->link = $request->url;
            $menu->menu = $request->menu;

            $menu->save();

            return response([
                'status' => 'success',
                'message' => 'Menu guardado correctamente'
            ])->status(200);

        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function destroy($id)
    {
        try {
            $menu = AdminMenuItem::find($id);
            $menu->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Menu eliminado correctamente'
            ])->status(200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar el menu'
            ])->status(500);
        }
    }


    public function orderMenuitem(Request $request){
        try {

            //primero obtienes el menu y hazle el json decode
            $menuItem = json_decode($request->menuItem, true);
            //recorres el array y vas actualizando el orden
            foreach ($menuItem as $key => $item) {
                $menu = AdminMenuItem::find($item['id']);
                $menu->sort = $key+1;
                $menu->save();
            }

            return response()->json([
                'message' => 'Orden actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);

        }
    }


}
