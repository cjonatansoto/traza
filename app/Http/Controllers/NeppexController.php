<?php

namespace App\Http\Controllers;


use App\Http\Requests\Neppex\CreateNeppexRequest;
use App\Libraries\Excel;
use App\Models\BorderCrossing;
use App\Models\Box;
use App\Models\Consignee;
use App\Models\Country;
use App\Models\DestinationPort;
use App\Models\Exporter;
use App\Models\NeppexControl;
use App\Models\ShippingPort;
use App\Models\SlaughterPlace;
use App\Models\SlaughterPlaceNeppex;
use App\Models\StorageLocation;
use App\Models\StoreLocationNeppex;
use App\Models\Transport;
use App\Models\User;
use App\Models\ViewNeppex;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use PhpParser\Node\Expr\Cast\Object_;
use RealRashid\SweetAlert\Facades\Alert;

class NeppexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $neppexs = NeppexControl::all();
        return view('neppexControls.index', compact('neppexs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shippingPorts = ShippingPort::where("inactive", "=", 0)->get();
        $countries = Country::where("inactive", "=", 0)->get();
        $destinationPorts = DestinationPort::where("inactive", "=", 0)->get();
        $exporters = Exporter::where("inactive", "=", 0)->get();
        $borderCrossings = BorderCrossing::where("inactive", "=", 0)->get();
        $consignees = Consignee::where("inactive", "=", 0)->get();
        $storageLocations = StorageLocation::where("inactive", "=", 0)->get();
        $slaughterPlaces = SlaughterPlace::where("inactive", "=", 0)->get();
        $transports = Transport::all();

        return view('neppexControls.create', compact('shippingPorts',
            'countries',
            'destinationPorts',
            'exporters',
            'borderCrossings',
            'consignees',
            'storageLocations',
            'slaughterPlaces',
            'transports'));
    }

    public function lastrecord(){
        $neppex =  DB::table('neppex_controls')->latest('updated_at')->first();
        $shippingPorts = ShippingPort::where("inactive", "=", 0)->get();
        $countries = Country::where("inactive", "=", 0)->get();
        $destinationPorts = DestinationPort::where("inactive", "=", 0)->get();
        $exporters = Exporter::where("inactive", "=", 0)->get();
        $borderCrossings = BorderCrossing::where("inactive", "=", 0)->get();
        $consignees = Consignee::where("inactive", "=", 0)->get();
        $storageLocations = StorageLocation::where("inactive", "=", 0)->get();
        $slaughterPlaces = SlaughterPlace::where("inactive", "=", 0)->get();
        $storageLocationsNeppex = StoreLocationNeppex::where("neppex_control_id", "=", $neppex->id)->get();
        $slaughterPlacesNeppex = SlaughterPlaceNeppex::where("neppex_control_id", "=", $neppex->id)->get();
        $transports = Transport::all();

        return view('neppexControls.lastrecord', compact('shippingPorts',
            'countries',
            'destinationPorts',
            'exporters',
            'borderCrossings',
            'consignees',
            'storageLocations',
            'slaughterPlaces',
            'neppex',
            'storageLocationsNeppex',
            'slaughterPlacesNeppex',
            'transports'
        ));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateNeppexRequest $request)
    {
        if($request){

            $boxes = [];

            $path = 'files/txts/' . time() . '.' . $request->box->extension();

            $request->box->move(public_path('files/txts'), time() . '.' . $request->box->extension());

            $fh = fopen($path,'r');

            while ($line = fgets($fh)) {

                $overallBox = (integer) str_replace(',', ' ', $line);

                $validateBox = DB::select("select N_MotivoSalida as status from bdsystem.dbo.v_empaque where Caja_General = '{$overallBox}'");


                $overallBoxUnique = Box::where("overall_box", "=", $overallBox)->count();


                if($overallBoxUnique === 0){
                    if($validateBox){
                        if($validateBox[0]->status === "Despacho a Cliente"){
                            array_push($boxes, [
                                'overallBox' => $overallBox,
                                'message' => 'OK',
                                'status' => true
                            ]);
                        }else{
                            array_push($boxes, [
                                'overallBox' => $overallBox,
                                'message' => 'La caja ingresada no se encuentra con despacho a cliente, operación cancelada',
                                'status' => false
                            ]);
                        }
                    }else{
                        array_push($boxes, [
                            'overallBox' => $overallBox,
                            'message' => 'La caja ingresada no existe, operación cancelada',
                            'status' => false
                        ]);
                    }

                }else{
                    array_push($boxes, [
                        'overallBox' => $overallBox,
                        'message' => 'La caja ingresada ya existe en el sistema, operación cancelada',
                        'status' => false
                    ]);
                }
            }

            fclose($fh);

            unlink($path);


            $validateNeppex = 0;

            foreach ($boxes as $box){
                if($box['status'] !== true){
                    $validateNeppex = $validateNeppex + 1;
                }

            }




            if($validateNeppex == 0){
                $neppex = new NeppexControl();
                $neppex->codaut = $request->codaut;
                $neppex->transfer_code = $request->transfer_code;
                $neppex->issue_certificate = $request->issue_certificate;
                $neppex->transport_id = $request->transport_id;
                $neppex->authorization_date = date('Y-d-m',strtotime($request->authorization_date));
                $neppex->container = $request->container;
                $neppex->container_name = $request->container_name;
                $neppex->shipping_port_id = $request->shipping_port_id;
                $neppex->country_id = $request->country_id;
                $neppex->destination_port_id = $request->destination_port_id;
                $neppex->exporter_id = $request->export_id;
                $neppex->border_crossing_id = $request->border_crossing_id;
                $neppex->consignee_id = $request->consignee_id;
                $neppex->inactive = 0;
                $neppex->created_by = Auth::user()->id;
                $neppex->updated_by = Auth::user()->id;
                $neppex->created_at = date('Y-d-m H:i:s.v');
                $neppex->updated_at = date('Y-d-m H:i:s.v');
                $neppex->save();

                foreach ($request->storage_location_id as $item){
                    $storeLocationNeppex = new StoreLocationNeppex();
                    $storeLocationNeppex->neppex_control_id = $neppex->id;
                    $storeLocationNeppex->storage_location_id = $item;
                    $storeLocationNeppex->created_at = date('Y-m-d H:i:s');
                    $storeLocationNeppex->updated_at = date('Y-m-d H:i:s');
                    $storeLocationNeppex->save();
                }

                foreach ($request->slaughter_place_id as $item){
                    $slaughterPlaceNeppex = new SlaughterPlaceNeppex();
                    $slaughterPlaceNeppex->neppex_control_id = $neppex->id;
                    $slaughterPlaceNeppex->slaughter_place_id = $item;
                    $slaughterPlaceNeppex->created_at = date('Y-m-d H:i:s');
                    $slaughterPlaceNeppex->updated_at = date('Y-m-d H:i:s');
                    $slaughterPlaceNeppex->save();
                }

                foreach ($boxes as $row){

                    $box = new Box();
                    $box->overall_box = (integer) $row['overallBox'];
                    $box->neppex_control_id = $neppex->id;
                    $box->created_at = date('Y-m-d H:i:s');
                    $box->updated_at = date('Y-m-d H:i:s');
                    $box->save();

                }

                Alert::success('Exito', 'Carga exitosa');
                return redirect()->route('neppex.index');

            }else{
                session()->forget('boxes');
                session()->put('boxes', $boxes);
                return redirect()->route('neppex.errors');
            }


        }

    }

    public function errors(Request $request){
        if($boxes = session()->get('boxes')) {
            Alert::error('Error', 'Revisa motivos.');
            return view('neppexControls.error', compact('boxes'));
        }

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(NeppexControl $neppex)
    {
        if($neppex){
            SlaughterPlaceNeppex::where('neppex_control_id',$neppex->id)->delete();
            StoreLocationNeppex::where('neppex_control_id',$neppex->id)->delete();
            Box::where('neppex_control_id',$neppex->id)->delete();
            $neppex->delete();
            Alert::error('success','Neppex eliminado correctamente');
            return redirect()->route('neppex.index');
        }else{
            Alert::error('error','Neppex no encontrado');
            return redirect()->route('neppex.index');
        }

    }

    public function filteredoutBox(){
        return view('neppexControls.filteredoutbox');
    }

    public function filteredoutBoxStore(Request $request){
        $this->validate($request, [
            'overall_box' => 'required|integer',
        ]);

        $boxes = ViewNeppex::box($request->overall_box)->get();

        $validate = count($boxes);

        if($validate >= 1){

            $data = [];

            foreach ($boxes as $box) {
                array_push($data, [
                    'N_Codaut' => $box->N_Codaut,
                    'N_CodigoTraspaso' => $box->N_CodigoTraspaso,
                    'N_FechaAutorizacion' => $box->N_FechaAutorizacion,
                    'N_FechaCarga' => $box->N_FechaCarga,
                    'N_Contenedor' => $box->N_Contenedor,
                    'N_NombreContenedor' => $box->N_NombreContenedor,
                    'N_Pais' => $box->N_Pais,
                    'N_Transporte' => $box->N_Transporte,
                    'N_PuertoEmbarque' => $box->N_PuertoEmbarque,
                    'N_PuertoDestino' => $box->N_PuertoDestino,
                    'N_Exportador' => $box->N_Exportador,
                    'N_Aduana' => $box->N_Aduana,
                    'N_Consignatario' => $box->N_Consignatario,
                    'N_UsuarioTraza'  => $box->N_UsuarioTraza,
                    'N_EmisionCertificado'  => $box->N_EmisionCertificado,
                    'N_LugarFaena'  => $box->N_LugarFaena,
                    'N_LugarAlmacenamiento'  => $box->N_LugarAlmacenamiento,
                    'N_Equipo'  => $box->N_Equipo,
                    'cod_lote'  => $box->cod_lote,
                    'N_Pallet'  => $box->N_Pallet,
                    'N_Pos'  => $box->N_Pos,
                    'N_IDTurno'  => $box->N_IDTurno,
                    'N_Turno'  => $box->N_Turno,
                    'N_Lote'  => $box->N_Lote,
                    'Tipo_Proceso'  => $box->Tipo_Proceso,
                    'Estado_Lote'  => $box->Estado_Lote,
                    'N_TEXTO1Lote'  => $box->N_TEXTO1Lote,
                    'N_RestriccionMercado'  => $box->N_RestriccionMercado,
                    'limite'  => $box->limite,
                    'CodOT'  => $box->CodOT,
                    'N_OT'  => $box->N_OT,
                    'N_TEXTO1Ot'  => $box->N_TEXTO1Ot,
                    'N_TEXTO1Especie'  => $box->N_TEXTO1Especie,
                    'N_Especie'  => $box->N_Especie,
                    'N_TEXTO1Corte'  => $box->N_TEXTO1Corte,
                    'N_Corte'  => $box->N_Corte,
                    'N_TEXTO1Conservacion'  => $box->N_TEXTO1Conservacion,
                    'N_Conservacion'  => $box->N_Conservacion,
                    'N_TEXTO1Condicion'  => $box->N_TEXTO1Condicion,
                    'N_Condicion'  => $box->N_Condicion,
                    'Id_Producto'  => $box->Id_Producto,
                    'N_CODProducto'  => $box->N_CODProducto,
                    'Producto'  => $box->Producto,
                    'DescProd'  => $box->DescProd,
                    'N_TEXTO1Producto'  => $box->N_TEXTO1Producto,
                    'N_CODTerminacion'  => $box->N_CODTerminacion,
                    'N_Terminacion'  => $box->N_Terminacion,
                    'N_TEXTO1Envase'  => $box->N_TEXTO1Envase,
                    'N_Envase'  => $box->N_Envase,
                    'Empresa'  => $box->Empresa,
                    'N_TEXTO1Calidad'  => $box->N_TEXTO1Calidad,
                    'N_Calidad'  => $box->N_Calidad,
                    'N_TEXTO1Calibre'  => $box->N_TEXTO1Calibre,
                    'N_Calibre'  => $box->N_Calibre,
                    'N_CODUnidad'  => $box->N_CODUnidad,
                    'N_Unidad'  => $box->N_Unidad,
                    'Cliente'  => $box->Cliente,
                    'Usuario'  => $box->Usuario,
                    'Caja_Lote'  => $box->Caja_Lote,
                    'Caja_General'  => $box->Caja_General,
                    'Kg'  => $box->Kg,
                    'tara'  => $box->tara,
                    'N_Medida'  => $box->N_Medida,
                    'piezas'  => $box->piezas,
                    'Fecha_Frigo'  => $box->Fecha_Frigo,
                    'Fecha_Prod'  => $box->Fecha_Prod,
                    'Fecha_Cosecha'  => $box->Fecha_Cosecha,
                    'Registro_Sistema'  => $box->Registro_Sistema,
                    'N_PesoBruto'  => $box->N_PesoBruto,
                    'N_PNom'  => $box->N_PNom,
                    'N_CODOrigen'  => $box->N_CODOrigen,
                    'N_Origen'  => $box->N_Origen,
                    'N_Proveedor'  => $box->N_Proveedor,
                    'N_Jaula'  => $box->N_Jaula,
                    'N_Etiqueta1'  => $box->N_Etiqueta1,
                    'N_Etiqueta2'  => $box->N_Etiqueta2,
                    'N_PesoNeto'  => $box->N_PesoNeto,
                    'Fecha_Venc'  => $box->Fecha_Venc,
                    'N_Barra'  => $box->N_Barra,
                    'N_Tara'  => $box->N_Tara,
                    'N_Tara2'  => $box->N_Tara2,
                    'N_Contratista'  => $box->N_Contratista,
                    'N_Estado'  => $box->N_Estado,
                    'N_MotivoSalida'  => $box->N_MotivoSalida,
                    'N_IdEquipo'  => $box->N_IdEquipo,
                    'N_IdEti1'  => $box->N_IdEti1,
                    'N_IdEti2'  => $box->N_IdEti2,
                    'N_IdLote'  => $box->N_IdLote,
                    'Fecha_Despacho'  => $box->Fecha_Despacho,
                    'N_Contratista_Proceso'  => $box->N_Contratista_Proceso,
                    'N_Guia'  => $box->N_Guia,
                    'Piezas_Enteras'  => $box->Piezas_Enteras,
                    'N_PesoEtiqueta'  => $box->N_PesoEtiqueta,
                    'N_MMPP'  => $box->N_MMPP,
                    'N_BarraMinerva'  => $box->N_BarraMinerva,
                    'N_TEXTO1Desp'  => $box->N_TEXTO1Desp,
                    'N_TEXTO2Desp'  => $box->N_TEXTO2Desp,
                    'N_TEXTO3Desp'  => $box->N_TEXTO3Desp,
                    'N_Embarque'  => $box->N_Embarque,
                    'N_CertfASC'  => $box->N_CertfASC,
                    'N_NumCerfASC'  => $box->N_NumCerfASC,
                    'N_BapEstrellas'  => $box->N_BapEstrellas,
                    'N_Ano'  => $box->N_Ano,
                    'N_Mes'  => $box->N_Mes,
                    'N_PesoNom2' => $box->N_PesoNom2
                ]);
            }

            $excel     = new Excel([
                'pathfile' => null,
                'filename' => 'NEPPEX_FILTRO',
                'title' => 'REPORTE_NEPPEX',
                'columns' => [
                    'N_Codaut',
                    'N_CodigoTraspaso',
                    'N_FechaAutorizacion',
                    'N_FechaCarga',
                    'N_Contenedor',
                    'N_NombreContenedor',
                    'N_Pais',
                    'N_Transporte',
                    'N_PuertoEmbarque',
                    'N_PuertoDestino',
                    'N_Exportador',
                    'N_Aduana',
                    'N_Consignatario',
                    'N_UsuarioTraza',
                    'N_EmisionCertificado',
                    'N_LugarFaena',
                    'N_LugarAlmacenamiento',
                    'N_Equipo',
                    'cod_lote',
                    'N_Pallet',
                    'N_Pos',
                    'N_IDTurno',
                    'N_Turno',
                    'N_Lote',
                    'Tipo_Proceso',
                    'Estado_Lote',
                    'N_TEXTO1Lote',
                    'N_RestriccionMercado',
                    'limite',
                    'CodOT',
                    'N_OT',
                    'N_TEXTO1Ot',
                    'N_TEXTO1Especie',
                    'N_Especie',
                    'N_TEXTO1Corte',
                    'N_Corte',
                    'N_TEXTO1Conservacion',
                    'N_Conservacion',
                    'N_TEXTO1Condicion',
                    'N_Condicion',
                    'Id_Producto',
                    'N_CODProducto',
                    'Producto',
                    'DescProd',
                    'N_TEXTO1Producto',
                    'N_CODTerminacion',
                    'N_Terminacion',
                    'N_TEXTO1Envase',
                    'N_Envase',
                    'Empresa',
                    'N_TEXTO1Calidad',
                    'N_Calidad',
                    'N_TEXTO1Calibre',
                    'N_Calibre',
                    'N_CODUnidad',
                    'N_Unidad',
                    'Cliente',
                    'Usuario',
                    'Caja_Lote',
                    'Caja_General',
                    'Kg',
                    'tara',
                    'N_Medida',
                    'piezas',
                    'Fecha_Frigo',
                    'Fecha_Prod',
                    'Fecha_Cosecha',
                    'Registro_Sistema',
                    'N_PesoBruto',
                    'N_PNom',
                    'N_CODOrigen',
                    'N_Origen',
                    'N_Proveedor',
                    'N_Jaula',
                    'N_Etiqueta1',
                    'N_Etiqueta2',
                    'N_PesoNeto',
                    'Fecha_Venc',
                    'N_Barra',
                    'N_Tara',
                    'N_Tara2',
                    'N_Contratista',
                    'N_Estado',
                    'N_MotivoSalida',
                    'N_IdEquipo',
                    'N_IdEti1',
                    'N_IdEti2',
                    'N_IdLote',
                    'Fecha_Despacho',
                    'N_Contratista_Proceso',
                    'N_Guia',
                    'Piezas_Enteras',
                    'N_PesoEtiqueta',
                    'N_MMPP',
                    'N_BarraMinerva',
                    'N_TEXTO1Desp',
                    'N_TEXTO2Desp',
                    'N_TEXTO3Desp',
                    'N_Embarque',
                    'N_CertfASC',
                    'N_NumCerfASC',
                    'N_BapEstrellas',
                    'N_Ano',
                    'N_Mes',
                    'N_PesoNom2'
                ]
            ]);

            $excel->setValuesArray($data, [
                'N_Codaut',
                'N_CodigoTraspaso',
                'N_FechaAutorizacion',
                'N_FechaCarga',
                'N_Contenedor',
                'N_NombreContenedor',
                'N_Pais',
                'N_Transporte',
                'N_PuertoEmbarque',
                'N_PuertoDestino',
                'N_Exportador',
                'N_Aduana',
                'N_Consignatario',
                'N_UsuarioTraza',
                'N_EmisionCertificado',
                'N_LugarFaena',
                'N_LugarAlmacenamiento',
                'N_Equipo',
                'cod_lote',
                'N_Pallet',
                'N_Pos',
                'N_IDTurno',
                'N_Turno',
                'N_Lote',
                'Tipo_Proceso',
                'Estado_Lote',
                'N_TEXTO1Lote',
                'N_RestriccionMercado',
                'limite',
                'CodOT',
                'N_OT',
                'N_TEXTO1Ot',
                'N_TEXTO1Especie',
                'N_Especie',
                'N_TEXTO1Corte',
                'N_Corte',
                'N_TEXTO1Conservacion',
                'N_Conservacion',
                'N_TEXTO1Condicion',
                'N_Condicion',
                'Id_Producto',
                'N_CODProducto',
                'Producto',
                'DescProd',
                'N_TEXTO1Producto',
                'N_CODTerminacion',
                'N_Terminacion',
                'N_TEXTO1Envase',
                'N_Envase',
                'Empresa',
                'N_TEXTO1Calidad',
                'N_Calidad',
                'N_TEXTO1Calibre',
                'N_Calibre',
                'N_CODUnidad',
                'N_Unidad',
                'Cliente',
                'Usuario',
                'Caja_Lote',
                'Caja_General',
                'Kg',
                'tara',
                'N_Medida',
                'piezas',
                'Fecha_Frigo',
                'Fecha_Prod',
                'Fecha_Cosecha',
                'Registro_Sistema',
                'N_PesoBruto',
                'N_PNom',
                'N_CODOrigen',
                'N_Origen',
                'N_Proveedor',
                'N_Jaula',
                'N_Etiqueta1',
                'N_Etiqueta2',
                'N_PesoNeto',
                'Fecha_Venc',
                'N_Barra',
                'N_Tara',
                'N_Tara2',
                'N_Contratista',
                'N_Estado',
                'N_MotivoSalida',
                'N_IdEquipo',
                'N_IdEti1',
                'N_IdEti2',
                'N_IdLote',
                'Fecha_Despacho',
                'N_Contratista_Proceso',
                'N_Guia',
                'Piezas_Enteras',
                'N_PesoEtiqueta',
                'N_MMPP',
                'N_BarraMinerva',
                'N_TEXTO1Desp',
                'N_TEXTO2Desp',
                'N_TEXTO3Desp',
                'N_Embarque',
                'N_CertfASC',
                'N_NumCerfASC',
                'N_BapEstrellas',
                'N_Ano',
                'N_Mes',
                'N_PesoNom2'
            ]);

            $excel->save();

        }else{

            Alert::error('error', 'No se han encontrado para filro aplicado');
            return redirect()->route('neppex.filteredoutbox');
        }


    }


    public function filteredout(){
        $countries = Country::where("inactive", "=", 0)->get();
        $exporters = Exporter::where("inactive", "=", 0)->get();
        return view('neppexControls.filteredout', compact('countries', 'exporters'));
    }

    public function filteredoutstore(Request $request){

        $boxes = ViewNeppex::authorizationDate($request->start_date_authorization, $request->end_date_authorization)
            ->loadDate($request->start_date_load, $request->end_date_load)
            ->codaut($request->codaut)
            ->transferCode($request->transfer_code)
            ->country($request->country_id)
            ->exporter($request->export_id)->get();

        $validate = count($boxes);

        if($validate >= 1){

            $data = [];

            foreach ($boxes as $box) {
                array_push($data, [
                    'N_Codaut' => $box->N_Codaut,
                    'N_CodigoTraspaso' => $box->N_CodigoTraspaso,
                    'N_FechaAutorizacion' => $box->N_FechaAutorizacion,
                    'N_FechaCarga' => $box->N_FechaCarga,
                    'N_Contenedor' => $box->N_Contenedor,
                    'N_NombreContenedor' => $box->N_NombreContenedor,
                    'N_Pais' => $box->N_Pais,
                    'N_Transporte' => $box->N_Transporte,
                    'N_PuertoEmbarque' => $box->N_PuertoEmbarque,
                    'N_PuertoDestino' => $box->N_PuertoDestino,
                    'N_Exportador' => $box->N_Exportador,
                    'N_Aduana' => $box->N_Aduana,
                    'N_Consignatario' => $box->N_Consignatario,
                    'N_UsuarioTraza'  => $box->N_UsuarioTraza,
                    'N_EmisionCertificado'  => $box->N_EmisionCertificado,
                    'N_LugarFaena'  => $box->N_LugarFaena,
                    'N_LugarAlmacenamiento'  => $box->N_LugarAlmacenamiento,
                    'N_Equipo'  => $box->N_Equipo,
                    'cod_lote'  => $box->cod_lote,
                    'N_Pallet'  => $box->N_Pallet,
                    'N_Pos'  => $box->N_Pos,
                    'N_IDTurno'  => $box->N_IDTurno,
                    'N_Turno'  => $box->N_Turno,
                    'N_Lote'  => $box->N_Lote,
                    'Tipo_Proceso'  => $box->Tipo_Proceso,
                    'Estado_Lote'  => $box->Estado_Lote,
                    'N_TEXTO1Lote'  => $box->N_TEXTO1Lote,
                    'N_RestriccionMercado'  => $box->N_RestriccionMercado,
                    'limite'  => $box->limite,
                    'CodOT'  => $box->CodOT,
                    'N_OT'  => $box->N_OT,
                    'N_TEXTO1Ot'  => $box->N_TEXTO1Ot,
                    'N_TEXTO1Especie'  => $box->N_TEXTO1Especie,
                    'N_Especie'  => $box->N_Especie,
                    'N_TEXTO1Corte'  => $box->N_TEXTO1Corte,
                    'N_Corte'  => $box->N_Corte,
                    'N_TEXTO1Conservacion'  => $box->N_TEXTO1Conservacion,
                    'N_Conservacion'  => $box->N_Conservacion,
                    'N_TEXTO1Condicion'  => $box->N_TEXTO1Condicion,
                    'N_Condicion'  => $box->N_Condicion,
                    'Id_Producto'  => $box->Id_Producto,
                    'N_CODProducto'  => $box->N_CODProducto,
                    'Producto'  => $box->Producto,
                    'DescProd'  => $box->DescProd,
                    'N_TEXTO1Producto'  => $box->N_TEXTO1Producto,
                    'N_CODTerminacion'  => $box->N_CODTerminacion,
                    'N_Terminacion'  => $box->N_Terminacion,
                    'N_TEXTO1Envase'  => $box->N_TEXTO1Envase,
                    'N_Envase'  => $box->N_Envase,
                    'Empresa'  => $box->Empresa,
                    'N_TEXTO1Calidad'  => $box->N_TEXTO1Calidad,
                    'N_Calidad'  => $box->N_Calidad,
                    'N_TEXTO1Calibre'  => $box->N_TEXTO1Calibre,
                    'N_Calibre'  => $box->N_Calibre,
                    'N_CODUnidad'  => $box->N_CODUnidad,
                    'N_Unidad'  => $box->N_Unidad,
                    'Cliente'  => $box->Cliente,
                    'Usuario'  => $box->Usuario,
                    'Caja_Lote'  => $box->Caja_Lote,
                    'Caja_General'  => $box->Caja_General,
                    'Kg'  => $box->Kg,
                    'tara'  => $box->tara,
                    'N_Medida'  => $box->N_Medida,
                    'piezas'  => $box->piezas,
                    'Fecha_Frigo'  => $box->Fecha_Frigo,
                    'Fecha_Prod'  => $box->Fecha_Prod,
                    'Fecha_Cosecha'  => $box->Fecha_Cosecha,
                    'Registro_Sistema'  => $box->Registro_Sistema,
                    'N_PesoBruto'  => $box->N_PesoBruto,
                    'N_PNom'  => $box->N_PNom,
                    'N_CODOrigen'  => $box->N_CODOrigen,
                    'N_Origen'  => $box->N_Origen,
                    'N_Proveedor'  => $box->N_Proveedor,
                    'N_Jaula'  => $box->N_Jaula,
                    'N_Etiqueta1'  => $box->N_Etiqueta1,
                    'N_Etiqueta2'  => $box->N_Etiqueta2,
                    'N_PesoNeto'  => $box->N_PesoNeto,
                    'Fecha_Venc'  => $box->Fecha_Venc,
                    'N_Barra'  => $box->N_Barra,
                    'N_Tara'  => $box->N_Tara,
                    'N_Tara2'  => $box->N_Tara2,
                    'N_Contratista'  => $box->N_Contratista,
                    'N_Estado'  => $box->N_Estado,
                    'N_MotivoSalida'  => $box->N_MotivoSalida,
                    'N_IdEquipo'  => $box->N_IdEquipo,
                    'N_IdEti1'  => $box->N_IdEti1,
                    'N_IdEti2'  => $box->N_IdEti2,
                    'N_IdLote'  => $box->N_IdLote,
                    'Fecha_Despacho'  => $box->Fecha_Despacho,
                    'N_Contratista_Proceso'  => $box->N_Contratista_Proceso,
                    'N_Guia'  => $box->N_Guia,
                    'Piezas_Enteras'  => $box->Piezas_Enteras,
                    'N_PesoEtiqueta'  => $box->N_PesoEtiqueta,
                    'N_MMPP'  => $box->N_MMPP,
                    'N_BarraMinerva'  => $box->N_BarraMinerva,
                    'N_TEXTO1Desp'  => $box->N_TEXTO1Desp,
                    'N_TEXTO2Desp'  => $box->N_TEXTO2Desp,
                    'N_TEXTO3Desp'  => $box->N_TEXTO3Desp,
                    'N_Embarque'  => $box->N_Embarque,
                    'N_CertfASC'  => $box->N_CertfASC,
                    'N_NumCerfASC'  => $box->N_NumCerfASC,
                    'N_BapEstrellas'  => $box->N_BapEstrellas,
                    'N_Ano'  => $box->N_Ano,
                    'N_Mes'  => $box->N_Mes,
                    'N_PesoNom2' => $box->N_PesoNom2
                ]);
            }

            $excel     = new Excel([
                'pathfile' => null,
                'filename' => 'NEPPEX_FILTRO',
                'title' => 'REPORTE_NEPPEX',
                'columns' => [
                    'N_Codaut',
                    'N_CodigoTraspaso',
                    'N_FechaAutorizacion',
                    'N_FechaCarga',
                    'N_Contenedor',
                    'N_NombreContenedor',
                    'N_Pais',
                    'N_Transporte',
                    'N_PuertoEmbarque',
                    'N_PuertoDestino',
                    'N_Exportador',
                    'N_Aduana',
                    'N_Consignatario',
                    'N_UsuarioTraza',
                    'N_EmisionCertificado',
                    'N_LugarFaena',
                    'N_LugarAlmacenamiento',
                    'N_Equipo',
                    'cod_lote',
                    'N_Pallet',
                    'N_Pos',
                    'N_IDTurno',
                    'N_Turno',
                    'N_Lote',
                    'Tipo_Proceso',
                    'Estado_Lote',
                    'N_TEXTO1Lote',
                    'N_RestriccionMercado',
                    'limite',
                    'CodOT',
                    'N_OT',
                    'N_TEXTO1Ot',
                    'N_TEXTO1Especie',
                    'N_Especie',
                    'N_TEXTO1Corte',
                    'N_Corte',
                    'N_TEXTO1Conservacion',
                    'N_Conservacion',
                    'N_TEXTO1Condicion',
                    'N_Condicion',
                    'Id_Producto',
                    'N_CODProducto',
                    'Producto',
                    'DescProd',
                    'N_TEXTO1Producto',
                    'N_CODTerminacion',
                    'N_Terminacion',
                    'N_TEXTO1Envase',
                    'N_Envase',
                    'Empresa',
                    'N_TEXTO1Calidad',
                    'N_Calidad',
                    'N_TEXTO1Calibre',
                    'N_Calibre',
                    'N_CODUnidad',
                    'N_Unidad',
                    'Cliente',
                    'Usuario',
                    'Caja_Lote',
                    'Caja_General',
                    'Kg',
                    'tara',
                    'N_Medida',
                    'piezas',
                    'Fecha_Frigo',
                    'Fecha_Prod',
                    'Fecha_Cosecha',
                    'Registro_Sistema',
                    'N_PesoBruto',
                    'N_PNom',
                    'N_CODOrigen',
                    'N_Origen',
                    'N_Proveedor',
                    'N_Jaula',
                    'N_Etiqueta1',
                    'N_Etiqueta2',
                    'N_PesoNeto',
                    'Fecha_Venc',
                    'N_Barra',
                    'N_Tara',
                    'N_Tara2',
                    'N_Contratista',
                    'N_Estado',
                    'N_MotivoSalida',
                    'N_IdEquipo',
                    'N_IdEti1',
                    'N_IdEti2',
                    'N_IdLote',
                    'Fecha_Despacho',
                    'N_Contratista_Proceso',
                    'N_Guia',
                    'Piezas_Enteras',
                    'N_PesoEtiqueta',
                    'N_MMPP',
                    'N_BarraMinerva',
                    'N_TEXTO1Desp',
                    'N_TEXTO2Desp',
                    'N_TEXTO3Desp',
                    'N_Embarque',
                    'N_CertfASC',
                    'N_NumCerfASC',
                    'N_BapEstrellas',
                    'N_Ano',
                    'N_Mes',
                    'N_PesoNom2'
                ]
            ]);

            $excel->setValuesArray($data, [
                'N_Codaut',
                'N_CodigoTraspaso',
                'N_FechaAutorizacion',
                'N_FechaCarga',
                'N_Contenedor',
                'N_NombreContenedor',
                'N_Pais',
                'N_Transporte',
                'N_PuertoEmbarque',
                'N_PuertoDestino',
                'N_Exportador',
                'N_Aduana',
                'N_Consignatario',
                'N_UsuarioTraza',
                'N_EmisionCertificado',
                'N_LugarFaena',
                'N_LugarAlmacenamiento',
                'N_Equipo',
                'cod_lote',
                'N_Pallet',
                'N_Pos',
                'N_IDTurno',
                'N_Turno',
                'N_Lote',
                'Tipo_Proceso',
                'Estado_Lote',
                'N_TEXTO1Lote',
                'N_RestriccionMercado',
                'limite',
                'CodOT',
                'N_OT',
                'N_TEXTO1Ot',
                'N_TEXTO1Especie',
                'N_Especie',
                'N_TEXTO1Corte',
                'N_Corte',
                'N_TEXTO1Conservacion',
                'N_Conservacion',
                'N_TEXTO1Condicion',
                'N_Condicion',
                'Id_Producto',
                'N_CODProducto',
                'Producto',
                'DescProd',
                'N_TEXTO1Producto',
                'N_CODTerminacion',
                'N_Terminacion',
                'N_TEXTO1Envase',
                'N_Envase',
                'Empresa',
                'N_TEXTO1Calidad',
                'N_Calidad',
                'N_TEXTO1Calibre',
                'N_Calibre',
                'N_CODUnidad',
                'N_Unidad',
                'Cliente',
                'Usuario',
                'Caja_Lote',
                'Caja_General',
                'Kg',
                'tara',
                'N_Medida',
                'piezas',
                'Fecha_Frigo',
                'Fecha_Prod',
                'Fecha_Cosecha',
                'Registro_Sistema',
                'N_PesoBruto',
                'N_PNom',
                'N_CODOrigen',
                'N_Origen',
                'N_Proveedor',
                'N_Jaula',
                'N_Etiqueta1',
                'N_Etiqueta2',
                'N_PesoNeto',
                'Fecha_Venc',
                'N_Barra',
                'N_Tara',
                'N_Tara2',
                'N_Contratista',
                'N_Estado',
                'N_MotivoSalida',
                'N_IdEquipo',
                'N_IdEti1',
                'N_IdEti2',
                'N_IdLote',
                'Fecha_Despacho',
                'N_Contratista_Proceso',
                'N_Guia',
                'Piezas_Enteras',
                'N_PesoEtiqueta',
                'N_MMPP',
                'N_BarraMinerva',
                'N_TEXTO1Desp',
                'N_TEXTO2Desp',
                'N_TEXTO3Desp',
                'N_Embarque',
                'N_CertfASC',
                'N_NumCerfASC',
                'N_BapEstrellas',
                'N_Ano',
                'N_Mes',
                'N_PesoNom2'
            ]);

            $excel->save();

        }else{

            Alert::error('error', 'No se han encontrado para filro aplicado');
            return redirect()->route('neppex.filteredout');
        }



    }


    public function generateExcel($codaut){
        set_time_limit(300);

        $boxes = ViewNeppex::codaut($codaut)->get();

        $data = [];
        foreach ($boxes as $box) {
            array_push($data, [
                'N_Codaut' => $box->N_Codaut,
                'N_CodigoTraspaso' => $box->N_CodigoTraspaso,
                'N_FechaAutorizacion' => $box->N_FechaAutorizacion,
                'N_FechaCarga' => $box->N_FechaCarga,
                'N_Contenedor' => $box->N_Contenedor,
                'N_NombreContenedor' => $box->N_NombreContenedor,
                'N_Pais' => $box->N_Pais,
                'N_Transporte' => $box->N_Transporte,
                'N_PuertoEmbarque' => $box->N_PuertoEmbarque,
                'N_PuertoDestino' => $box->N_PuertoDestino,
                'N_Exportador' => $box->N_Exportador,
                'N_Aduana' => $box->N_Aduana,
                'N_Consignatario' => $box->N_Consignatario,
                'N_UsuarioTraza'  => $box->N_UsuarioTraza,
                'N_EmisionCertificado'  => $box->N_EmisionCertificado,
                'N_LugarFaena'  => $box->N_LugarFaena,
                'N_LugarAlmacenamiento'  => $box->N_LugarAlmacenamiento,
                'N_Equipo'  => $box->N_Equipo,
                'cod_lote'  => $box->cod_lote,
                'N_Pallet'  => $box->N_Pallet,
                'N_Pos'  => $box->N_Pos,
                'N_IDTurno'  => $box->N_IDTurno,
                'N_Turno'  => $box->N_Turno,
                'N_Lote'  => $box->N_Lote,
                'Tipo_Proceso'  => $box->Tipo_Proceso,
                'Estado_Lote'  => $box->Estado_Lote,
                'N_TEXTO1Lote'  => $box->N_TEXTO1Lote,
                'N_RestriccionMercado'  => $box->N_RestriccionMercado,
                'limite'  => $box->limite,
                'CodOT'  => $box->CodOT,
                'N_OT'  => $box->N_OT,
                'N_TEXTO1Ot'  => $box->N_TEXTO1Ot,
                'N_TEXTO1Especie'  => $box->N_TEXTO1Especie,
                'N_Especie'  => $box->N_Especie,
                'N_TEXTO1Corte'  => $box->N_TEXTO1Corte,
                'N_Corte'  => $box->N_Corte,
                'N_TEXTO1Conservacion'  => $box->N_TEXTO1Conservacion,
                'N_Conservacion'  => $box->N_Conservacion,
                'N_TEXTO1Condicion'  => $box->N_TEXTO1Condicion,
                'N_Condicion'  => $box->N_Condicion,
                'Id_Producto'  => $box->Id_Producto,
                'N_CODProducto'  => $box->N_CODProducto,
                'Producto'  => $box->Producto,
                'DescProd'  => $box->DescProd,
                'N_TEXTO1Producto'  => $box->N_TEXTO1Producto,
                'N_CODTerminacion'  => $box->N_CODTerminacion,
                'N_Terminacion'  => $box->N_Terminacion,
                'N_TEXTO1Envase'  => $box->N_TEXTO1Envase,
                'N_Envase'  => $box->N_Envase,
                'Empresa'  => $box->Empresa,
                'N_TEXTO1Calidad'  => $box->N_TEXTO1Calidad,
                'N_Calidad'  => $box->N_Calidad,
                'N_TEXTO1Calibre'  => $box->N_TEXTO1Calibre,
                'N_Calibre'  => $box->N_Calibre,
                'N_CODUnidad'  => $box->N_CODUnidad,
                'N_Unidad'  => $box->N_Unidad,
                'Cliente'  => $box->Cliente,
                'Usuario'  => $box->Usuario,
                'Caja_Lote'  => $box->Caja_Lote,
                'Caja_General'  => $box->Caja_General,
                'Kg'  => $box->Kg,
                'tara'  => $box->tara,
                'N_Medida'  => $box->N_Medida,
                'piezas'  => $box->piezas,
                'Fecha_Frigo'  => $box->Fecha_Frigo,
                'Fecha_Prod'  => $box->Fecha_Prod,
                'Fecha_Cosecha'  => $box->Fecha_Cosecha,
                'Registro_Sistema'  => $box->Registro_Sistema,
                'N_PesoBruto'  => $box->N_PesoBruto,
                'N_PNom'  => $box->N_PNom,
                'N_CODOrigen'  => $box->N_CODOrigen,
                'N_Origen'  => $box->N_Origen,
                'N_Proveedor'  => $box->N_Proveedor,
                'N_Jaula'  => $box->N_Jaula,
                'N_Etiqueta1'  => $box->N_Etiqueta1,
                'N_Etiqueta2'  => $box->N_Etiqueta2,
                'N_PesoNeto'  => $box->N_PesoNeto,
                'Fecha_Venc'  => $box->Fecha_Venc,
                'N_Barra'  => $box->N_Barra,
                'N_Tara'  => $box->N_Tara,
                'N_Tara2'  => $box->N_Tara2,
                'N_Contratista'  => $box->N_Contratista,
                'N_Estado'  => $box->N_Estado,
                'N_MotivoSalida'  => $box->N_MotivoSalida,
                'N_IdEquipo'  => $box->N_IdEquipo,
                'N_IdEti1'  => $box->N_IdEti1,
                'N_IdEti2'  => $box->N_IdEti2,
                'N_IdLote'  => $box->N_IdLote,
                'Fecha_Despacho'  => $box->Fecha_Despacho,
                'N_Contratista_Proceso'  => $box->N_Contratista_Proceso,
                'N_Guia'  => $box->N_Guia,
                'Piezas_Enteras'  => $box->Piezas_Enteras,
                'N_PesoEtiqueta'  => $box->N_PesoEtiqueta,
                'N_MMPP'  => $box->N_MMPP,
                'N_BarraMinerva'  => $box->N_BarraMinerva,
                'N_TEXTO1Desp'  => $box->N_TEXTO1Desp,
                'N_TEXTO2Desp'  => $box->N_TEXTO2Desp,
                'N_TEXTO3Desp'  => $box->N_TEXTO3Desp,
                'N_Embarque'  => $box->N_Embarque,
                'N_CertfASC'  => $box->N_CertfASC,
                'N_NumCerfASC'  => $box->N_NumCerfASC,
                'N_BapEstrellas'  => $box->N_BapEstrellas,
                'N_Ano'  => $box->N_Ano,
                'N_Mes'  => $box->N_Mes,
                'N_PesoNom2' => $box->N_PesoNom2
            ]);
        }

        $excel     = new Excel([
            'pathfile' => null,
            'filename' => 'REPORTE_NEPPEX_' . $codaut,
            'title' => 'REPORTE_NEPPEX',
            'columns' => [
                'N_Codaut',
                'N_CodigoTraspaso',
                'N_FechaAutorizacion',
                'N_FechaCarga',
                'N_Contenedor',
                'N_NombreContenedor',
                'N_Pais',
                'N_Transporte',
                'N_PuertoEmbarque',
                'N_PuertoDestino',
                'N_Exportador',
                'N_Aduana',
                'N_Consignatario',
                'N_UsuarioTraza',
                'N_EmisionCertificado',
                'N_LugarFaena',
                'N_LugarAlmacenamiento',
                'N_Equipo',
                'cod_lote',
                'N_Pallet',
                'N_Pos',
                'N_IDTurno',
                'N_Turno',
                'N_Lote',
                'Tipo_Proceso',
                'Estado_Lote',
                'N_TEXTO1Lote',
                'N_RestriccionMercado',
                'limite',
                'CodOT',
                'N_OT',
                'N_TEXTO1Ot',
                'N_TEXTO1Especie',
                'N_Especie',
                'N_TEXTO1Corte',
                'N_Corte',
                'N_TEXTO1Conservacion',
                'N_Conservacion',
                'N_TEXTO1Condicion',
                'N_Condicion',
                'Id_Producto',
                'N_CODProducto',
                'Producto',
                'DescProd',
                'N_TEXTO1Producto',
                'N_CODTerminacion',
                'N_Terminacion',
                'N_TEXTO1Envase',
                'N_Envase',
                'Empresa',
                'N_TEXTO1Calidad',
                'N_Calidad',
                'N_TEXTO1Calibre',
                'N_Calibre',
                'N_CODUnidad',
                'N_Unidad',
                'Cliente',
                'Usuario',
                'Caja_Lote',
                'Caja_General',
                'Kg',
                'tara',
                'N_Medida',
                'piezas',
                'Fecha_Frigo',
                'Fecha_Prod',
                'Fecha_Cosecha',
                'Registro_Sistema',
                'N_PesoBruto',
                'N_PNom',
                'N_CODOrigen',
                'N_Origen',
                'N_Proveedor',
                'N_Jaula',
                'N_Etiqueta1',
                'N_Etiqueta2',
                'N_PesoNeto',
                'Fecha_Venc',
                'N_Barra',
                'N_Tara',
                'N_Tara2',
                'N_Contratista',
                'N_Estado',
                'N_MotivoSalida',
                'N_IdEquipo',
                'N_IdEti1',
                'N_IdEti2',
                'N_IdLote',
                'Fecha_Despacho',
                'N_Contratista_Proceso',
                'N_Guia',
                'Piezas_Enteras',
                'N_PesoEtiqueta',
                'N_MMPP',
                'N_BarraMinerva',
                'N_TEXTO1Desp',
                'N_TEXTO2Desp',
                'N_TEXTO3Desp',
                'N_Embarque',
                'N_CertfASC',
                'N_NumCerfASC',
                'N_BapEstrellas',
                'N_Ano',
                'N_Mes',
                'N_PesoNom2'
            ]
        ]);

        $excel->setValuesArray($data, [
            'N_Codaut',
            'N_CodigoTraspaso',
            'N_FechaAutorizacion',
            'N_FechaCarga',
            'N_Contenedor',
            'N_NombreContenedor',
            'N_Pais',
            'N_Transporte',
            'N_PuertoEmbarque',
            'N_PuertoDestino',
            'N_Exportador',
            'N_Aduana',
            'N_Consignatario',
            'N_UsuarioTraza',
            'N_EmisionCertificado',
            'N_LugarFaena',
            'N_LugarAlmacenamiento',
            'N_Equipo',
            'cod_lote',
            'N_Pallet',
            'N_Pos',
            'N_IDTurno',
            'N_Turno',
            'N_Lote',
            'Tipo_Proceso',
            'Estado_Lote',
            'N_TEXTO1Lote',
            'N_RestriccionMercado',
            'limite',
            'CodOT',
            'N_OT',
            'N_TEXTO1Ot',
            'N_TEXTO1Especie',
            'N_Especie',
            'N_TEXTO1Corte',
            'N_Corte',
            'N_TEXTO1Conservacion',
            'N_Conservacion',
            'N_TEXTO1Condicion',
            'N_Condicion',
            'Id_Producto',
            'N_CODProducto',
            'Producto',
            'DescProd',
            'N_TEXTO1Producto',
            'N_CODTerminacion',
            'N_Terminacion',
            'N_TEXTO1Envase',
            'N_Envase',
            'Empresa',
            'N_TEXTO1Calidad',
            'N_Calidad',
            'N_TEXTO1Calibre',
            'N_Calibre',
            'N_CODUnidad',
            'N_Unidad',
            'Cliente',
            'Usuario',
            'Caja_Lote',
            'Caja_General',
            'Kg',
            'tara',
            'N_Medida',
            'piezas',
            'Fecha_Frigo',
            'Fecha_Prod',
            'Fecha_Cosecha',
            'Registro_Sistema',
            'N_PesoBruto',
            'N_PNom',
            'N_CODOrigen',
            'N_Origen',
            'N_Proveedor',
            'N_Jaula',
            'N_Etiqueta1',
            'N_Etiqueta2',
            'N_PesoNeto',
            'Fecha_Venc',
            'N_Barra',
            'N_Tara',
            'N_Tara2',
            'N_Contratista',
            'N_Estado',
            'N_MotivoSalida',
            'N_IdEquipo',
            'N_IdEti1',
            'N_IdEti2',
            'N_IdLote',
            'Fecha_Despacho',
            'N_Contratista_Proceso',
            'N_Guia',
            'Piezas_Enteras',
            'N_PesoEtiqueta',
            'N_MMPP',
            'N_BarraMinerva',
            'N_TEXTO1Desp',
            'N_TEXTO2Desp',
            'N_TEXTO3Desp',
            'N_Embarque',
            'N_CertfASC',
            'N_NumCerfASC',
            'N_BapEstrellas',
            'N_Ano',
            'N_Mes',
            'N_PesoNom2'
        ]);

        $excel->save();
    }
}
