@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Lugares de faena
            </h3>
        </div>
        <div class="section-body">
            <div class="row">
                @include('sweetalert::alert')
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <a class="btn btn-primary" href="{{route('slaughterplaces.create')}}"><i class="fa fa-plus-circle"></i> Nuevo registro</a>
                            <br/>
                            <br/>
                            <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#6777ef">
                                <tr>
                                    <th style="color:#fff;">#ID</th>
                                    <th style="color:#fff;">Nombre</th>
                                    <th style="color:#fff;">Cod. Sernapesca</th>
                                    <th style="color:#fff;">Ult. Edición</th>
                                    <th style="color:#fff;">Fecha Ult. Edición</th>
                                    <th style="color:#fff;">Estado</th>
                                    <th style="color:#fff;">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($slaughterPlaces as $slaughterPlace)
                                    <tr>
                                        <td>{{$slaughterPlace->id}}</td>
                                        <td>{{$slaughterPlace->name}}</td>
                                        <td>{{$slaughterPlace->code}}</td>
                                        <td>{{$slaughterPlace->updatedBy->name}}</td>
                                        <td>{{$slaughterPlace->updated_at->format('m-d-Y H:m:s')}}</td>
                                        <td>{{$slaughterPlace->inactive == 0 ? 'Activo' : 'Inactivo' }}</td>
                                        <td>
                                            <a href="{{ route('slaughterplaces.edit',$slaughterPlace->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Editar</a>
                                            @if($slaughterPlace->inactive == 0)
                                                <a href="{{route('slaughterplaces.movement', $slaughterPlace->id)}}" class="btn btn-danger btn-sm"><i class="fa fa-lock" aria-hidden="true"></i> Desactivar</a>
                                            @else
                                                <a href="{{route('slaughterplaces.movement', $slaughterPlace->id)}}" class="btn btn-success btn-sm"><i class="fa fa-unlock" aria-hidden="true"></i> Activar</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>#ID</th>
                                    <th>Nombre</th>
                                    <th>Cod. Sernapesca</th>
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
