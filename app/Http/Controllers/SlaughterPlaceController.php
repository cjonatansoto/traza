<?php

namespace App\Http\Controllers;

use App\Models\SlaughterPlace;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SlaughterPlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slaughterPlaces = SlaughterPlace::all();
        return view('slaughterPlaces.index', compact('slaughterPlaces'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('slaughterPlaces.create');
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
            'name' => 'required',
            'code' => 'required'
        ]);
        $input = $request->all();
        $slaughterPlace = SlaughterPlace::create($input);
        Alert::success('Exito', 'Registro creado!');
        return redirect()->route('slaughterplaces.index');
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
        $slaughterPlace = SlaughterPlace::find($id);
        return view('slaughterPlaces.edit',compact('slaughterPlace'));
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
            'name' => 'required',
            'code' => 'required'
        ]);

        $input = $request->all();
        $slaughterPlace = SlaughterPlace::find($id);
        $slaughterPlace->update($input);
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('slaughterplaces.index');
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

    public function movement(SlaughterPlace $slaughterPlace){
        $status = $slaughterPlace->inactive == 0 ? 1 : 0;
        $slaughterPlace->inactive = $status;
        $slaughterPlace->save();
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('slaughterplaces.index');
    }
}
