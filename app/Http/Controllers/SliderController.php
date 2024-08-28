<?php

namespace App\Http\Controllers;


use App\Models\Slider;

//use App\Repositories\SliderRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use mysql_xdevapi\Exception;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Image;

class SliderController extends AppBaseController
{


    /**
     * Display a listing of the Slider.
     *
     * @param Request $request
     * @return Response
     */
    public function getSlider()
    {
        try {
            $categoria = \request()->categoria;
            $sliders = Slider::where('categorias', '=', $categoria)
               // ->where('status', '=', 'enabled')
                ->orderBy('position', 'asc')
                ->get();
            return response($sliders, 200);

        } catch (\Exception $e) {
            $sliders = [];
            return response($e->getMessage(), 404);
        }
    }

    public function devolver_titulo($categoria)
    {
        $valor = '';

        if ($categoria == 'slider-grander') {
            $valor = 'Slider Principal';
        }

        if ($categoria == 'slider-seguridad') {
            $valor = 'Slider Seguridad';
        }

        if ($categoria == 'slider-bioseguridad') {
            $valor = 'Slider BioSeguridad';
        }

        if ($categoria == 'slider-pequeno') {
            $valor = 'Slider Entretenimiento';
        }

        return $valor;
    }

    public function store(Request $request, $id = 0)
    {
        try {
            $request->validate([
                'titulo' => 'required',
                'imagen' => $id == 0 ? 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' :
                                        'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $categoria = \request()->categoria;
            $imagen = 'default.png';

            if ($id > 0) {
                $slider = Slider::find($id);
                if ($slider) {
                    $imagen = $slider->imagen;
                }
            }

            if ($request->hasFile('imagen')) {
                $imageName = time() . '.' . $request->imagen->extension();
                $request->imagen->move(public_path('uploads/slider'), $imageName);
                $imagen = $imageName;
            }

            $slider = Slider::firstOrNew(['id' => $id]);
            $slider->titulo = $request->titulo;
            //$slider->descripcion = $request->has('descripcion') ? $request->descripcion : 'sin descripción';
            $slider->categorias = $categoria;
            $slider->imagen = $imagen;
            $slider->link = $request->has('link') ? $request->link : '#';
            $slider->status = $request->has('status') ? $request->status : 'enabled';

            $slider->save();

            // Set position as the next consecutive number
            $maxPosition = Slider::max('position');
            $slider->position = $maxPosition + 1;
            $slider->save();


            return response($slider, 200);

        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }

    public function destroy($id)
    {
        try {
            $slider = Slider::find($id);
            $slider->delete();
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }


    //ordenar los sliders
    public function ordenar(Request $request)
    {
        try {
           $sliders = json_decode($request->sliders, true);
            foreach ($sliders as $index => $slider) {
                $slider = Slider::find($slider['id']);
                $slider->position = $index + 1;
                $slider->save();
            }
            return response('success', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 404);
        }
    }


}
