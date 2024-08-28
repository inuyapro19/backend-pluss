<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCiudadRequest;
use App\Http\Requests\UpdateCiudadRequest;
//use App\Repositories\CiudadRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CiudadController extends AppBaseController
{
    /** @var  CiudadRepository */
    private $ciudadRepository;

    public function __construct()
    {
        $this->ciudadRepository =new Ciudad();
    }

    /**
     * Display a listing of the Ciudad.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        //$this->ciudadRepository->pushCriteria(new RequestCriteria($request));
        $ciudads = $this->ciudadRepository->all();

        return view('ciudads.index')
            ->with('ciudads', $ciudads);
    }

    /**
     * Show the form for creating a new Ciudad.
     *
     * @return Response
     */
    public function create()
    {
        return view('ciudads.create');
    }

    /**
     * Store a newly created Ciudad in storage.
     *
     * @param CreateCiudadRequest $request
     *
     * @return Response
     */
    public function store(CreateCiudadRequest $request)
    {
        $input = $request->all();

        $ciudad = $this->ciudadRepository->create($input);

        Flash::success('Ciudad saved successfully.');

        return redirect(route('ciudads.index'));
    }

    /**
     * Display the specified Ciudad.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $ciudad = $this->ciudadRepository->find($id);

        if (empty($ciudad)) {
            Flash::error('Ciudad not found');

            return redirect(route('ciudads.index'));
        }

        return view('ciudads.show')->with('ciudad', $ciudad);
    }

    /**
     * Show the form for editing the specified Ciudad.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $ciudad = $this->ciudadRepository->find($id);

        if (empty($ciudad)) {
            Flash::error('Ciudad not found');

            return redirect(route('ciudads.index'));
        }

        return view('ciudads.edit')->with('ciudad', $ciudad);
    }

    /**
     * Update the specified Ciudad in storage.
     *
     * @param  int              $id
     * @param UpdateCiudadRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCiudadRequest $request)
    {
        $ciudad = Ciudad::find($id);

        if (empty($ciudad)) {
            Flash::error('Ciudad no encontrada');

            return redirect(route('ciudads.index'));
        }

        $ciudad = $ciudad->update($request->all());

        Flash::success('Ciudad editada exitosamente');

        return redirect(route('ciudads.index'));
    }

    /**
     * Remove the specified Ciudad from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $ciudad = Ciudad::find($id);

        if (empty($ciudad)) {
            Flash::error('Ciudad not found');

            return redirect(route('ciudads.index'));
        }

        $ciudad->delete();

        Flash::success('Ciudad Eliminada exitosamente');

        return redirect(route('ciudads.index'));
    }
}
