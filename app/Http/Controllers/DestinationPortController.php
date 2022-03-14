<?php

namespace App\Http\Controllers;

use App\Models\DestinationPort;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class DestinationPortController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $destinationPorts = DestinationPort::all();
        return view('destinationPorts.index', compact('destinationPorts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('destinationPorts.create');
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
        $destinationPort = DestinationPort::create($input);
        return redirect()->route('destinationports.index');
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
        $destinationPort = DestinationPort::find($id);
        return view('destinationPorts.edit',compact('destinationPort'));
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
        $destinationPort = DestinationPort::find($id);
        $destinationPort->update($input);
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('destinationports.index');
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

    public function movement(DestinationPort $destinationPort){
        $status = $destinationPort->inactive == 0 ? 1 : 0;
        $destinationPort->inactive = $status;
        $destinationPort->save();
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('destinationports.index');
    }
}
