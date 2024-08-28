<?php

namespace App\Http\Controllers;

use App\Models\SliderVenta;
use Illuminate\Http\Request;
use Image;
use Laracasts\Flash\Flash;

class SliderVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = SliderVenta::where('tipo','=','slider')->get();

                return view('ventas.slider')
                    ->with('sliders', $sliders);
    }

    public function banner_index()
    {
        $sliders = SliderVenta::where('tipo','=','banner')->get();

        return view('ventas.banner')
            ->with('sliders', $sliders);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SliderVenta  $sliderVenta
     * @return \Illuminate\Http\Response
     */
    public function show(SliderVenta $sliderVenta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SliderVenta  $sliderVenta
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $slider = SliderVenta::find($id);
        return view('ventas.edit',['slider'=>$slider]);
    }

    public function edit_banner($id)
    {
        $slider = SliderVenta::find($id);
        return view('ventas.edit_banner',['slider'=>$slider]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SliderVenta  $sliderVenta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sliderVenta = SliderVenta::find($id);

        if ($request->hasFile('imagen')):
            $name = $request->imagen->getClientOriginalName();

            if (!file_exists('files/slider/')) {
                mkdir('files/slider/', 777, true);
            }
            $imagen = Image::make($request->imagen->getRealPath())->save('files/slider/' . $request->imagen->getClientOriginalName());
            $slider = $sliderVenta->update([
                'imagen' => $name,
                'tipo'=>'slider',
            ]);
         endif;

        Flash::success('Slider editado exitosamente');

        return redirect(route('ventas.slider'));
    }

    public function update_banner(Request $request, $id)
    {
        $sliderVenta = SliderVenta::find($id);

        if ($request->hasFile('imagen')):
            $name = $request->imagen->getClientOriginalName();

            if (!file_exists('files/banner/')) {
                mkdir('files/banner/', 777, true);
            }
            $imagen = Image::make($request->imagen->getRealPath())->save('files/banner/' . $request->imagen->getClientOriginalName());
            $slider = $sliderVenta->update([
                'imagen' => $name,
                'tipo'=>'banner',
            ]);
        endif;

        Flash::success('banner editado exitosamente');

        return redirect(route('ventas.banner'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SliderVenta  $sliderVenta
     * @return \Illuminate\Http\Response
     */
    public function destroy(SliderVenta $sliderVenta)
    {
        //
    }
}
