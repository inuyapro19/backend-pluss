<?php

namespace App\Http\Controllers;

use App\Models\AdminMenu;
use Illuminate\Http\Request;

class MenuController extends Controller
{

    public function index()
    {
        try {
            $menus = AdminMenu::with('items')->get();
            return response()->json([
                'status' => 'success',
                'data' => $menus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function storeMenu(Request $request)
    {

    }

    public function storeItem(Request $request, $menu)
    {

    }


}
