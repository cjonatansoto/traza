<?php

namespace App\Http\Controllers;

use App\Models\Consignee;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ConsigneeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $consignees = Consignee::all();
        return view('consignees.index', compact('consignees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('consignees.create');
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
            'description' => 'required'
        ]);
        $input = $request->all();
        $consignee = Consignee::create($input);
        Alert::success('Exito', 'Registro creado exitosamente!');
        return redirect()->route('consignees.index');
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
        $consignee = Consignee::find($id);
        return view('consignees.edit',compact('consignee'));
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
            'description' => 'required',
        ]);

        $input = $request->all();
        $consignee = Consignee::find($id);
        $consignee->update($input);
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('consignees.index');
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

    public function movement(Consignee $consignee){
        $status = $consignee->inactive == 0 ? 1 : 0;
        $consignee->inactive = $status;
        $consignee->save();
        Alert::success('Exito', 'Registro modificado!');
        return redirect()->route('consignees.index');
    }
}
