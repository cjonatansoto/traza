<?php

namespace App\Http\Controllers;

use App\Models\StorageLocation;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class StorageLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $storageLocations = StorageLocation::all();
        return view('storageLocations.index', compact('storageLocations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('storageLocations.create');
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
        $storageLocation = StorageLocation::create($input);
        Alert::success('Exito', 'Registro creado!');
        return redirect()->route('storagelocations.index');
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
        $storageLocation = StorageLocation::find($id);
        return view('storagelocations.edit',compact('storageLocation'));
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
        $storageLocation = StorageLocation::find($id);
        $storageLocation->update($input);
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('storagelocations.index');
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

    public function movement(StorageLocation $storageLocation){
        $status = $storageLocation->inactive == 0 ? 1 : 0;
        $storageLocation->inactive = $status;
        $storageLocation->save();
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('storagelocations.index');
    }
}
