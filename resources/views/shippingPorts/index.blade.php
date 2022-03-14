@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Puertos de embarque</h3>
        </div>
        <div class="section-body">
            <div class="row">
                @include('sweetalert::alert')
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <a class="btn btn-primary" href="{{route('shippingports.create')}}"><i class="fa fa-plus-circle"></i> Nuevo registro</a>
                            <br/>
                            <br/>
                            <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#6777ef">
                                <tr>
                                    <th style="color:#fff;">#ID</th>
                                    <th style="color:#fff;">Nombre</th>
                                    <th style="color:#fff;">Ult. Edición</th>
                                    <th style="color:#fff;">Fecha Ult. Edición</th>
                                    <th style="color:#fff;">Estado</th>
                                    <th style="color:#fff;">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($shippingPorts as $shippingPort)
                                    <tr>
                                        <td>{{$shippingPort->id}}</td>
                                        <td>{{$shippingPort->name}}</td>
                                        <td>{{$shippingPort->updatedBy->name}}</td>
                                        <td>{{$shippingPort->updated_at->format('m-d-Y H:m:s')}}</td>
                                        <td>{{$shippingPort->inactive == 0 ? 'Activo' : 'Inactivo' }}</td>
                                        <td>
                                            <a href="{{ route('shippingports.edit',$shippingPort->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Editar</a>
                                            @if($shippingPort->inactive == 0)
                                                <a href="{{route('shippingports.movement', $shippingPort->id)}}" class="btn btn-danger btn-sm"><i class="fa fa-lock" aria-hidden="true"></i> Desactivar</a>
                                            @else
                                                <a href="{{route('shippingports.movement', $shippingPort->id)}}" class="btn btn-success btn-sm"><i class="fa fa-unlock" aria-hidden="true"></i> Activar</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>#ID</th>
                                    <th>Nombre</th>
                                    <th>Ult. Edición</th>
                                    <th>Fecha Ult. Edición</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
