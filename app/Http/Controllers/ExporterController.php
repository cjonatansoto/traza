<?php

namespace App\Http\Controllers;

use App\Models\Exporter;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use ValidateRequests;

class ExporterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exporters = Exporter::all();
        return view('exporters.index', compact('exporters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('exporters.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'rut' => 'required|cl_rut',
            'name' => 'required',
        ]);
        $input = $request->all();
        $exporter = Exporter::create($input);
        Alert::success('Exito', 'Registro creado exitosamente!');
        return redirect()->route('exporters.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $exporter = Exporter::find($id);
        return view('exporters.edit',compact('exporter'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'rut' => 'required|cl_rut',
            'name' => 'required',
        ]);

        $input = $request->all();
        $exporter = Exporter::find($id);
        $exporter->update($input);
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('exporters.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function movement(Exporter $exporter){
        $status = $exporter->inactive == 0 ? 1 : 0;
        $exporter->inactive = $status;
        $exporter->save();
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('exporters.index');
    }
}
