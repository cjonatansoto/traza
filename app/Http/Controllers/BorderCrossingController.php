<?php

namespace App\Http\Controllers;

use App\Models\BorderCrossing;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class BorderCrossingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $borderCrossings = BorderCrossing::all();
        return view('borderCrossings.index', compact('borderCrossings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('borderCrossings.create');
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
            'name' => 'required'
        ]);
        $input = $request->all();
        $borderCrossing = BorderCrossing::create($input);
        Alert::success('Exito', 'Registro creado exitosamente!');
        return redirect()->route('bordercrossings.index');
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
        $borderCrossing = BorderCrossing::find($id);
        return view('borderCrossings.edit',compact('borderCrossing'));
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
            'name' => 'required'
        ]);

        $input = $request->all();
        $borderCrossing = BorderCrossing::find($id);
        $borderCrossing->update($input);
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('bordercrossings.index');
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

    public function movement(BorderCrossing $borderCrossing){
        $status = $borderCrossing->inactive == 0 ? 1 : 0;
        $borderCrossing->inactive = $status;
        $borderCrossing->save();
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('bordercrossings.index');
    }
}
