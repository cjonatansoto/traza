@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Paises</h3>
        </div>
        <div class="section-body">
            <div class="row">
                @include('sweetalert::alert')
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <a class="btn btn-primary" href="{{route('countries.create')}}"><i class="fa fa-plus-circle"></i> Nuevo registro</a>
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
                                @foreach($countries as $country)
                                <tr>
                                    <td>{{$country->id}}</td>
                                    <td>{{$country->name}}</td>
                                    <td>{{$country->updatedBy->name}}</td>
                                    <td>{{$country->updated_at->format('m-d-Y H:m:s')}}</td>
                                    <td>{{$country->inactive == 0 ? 'Activo' : 'Inactivo' }}</td>
                                    <td>
                                        <a href="{{ route('countries.edit',$country->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Editar</a>
                                        @if($country->inactive == 0)
                                            <a href="{{route('countries.movement', $country->id)}}" class="btn btn-danger btn-sm"><i class="fa fa-lock" aria-hidden="true"></i> Desactivar</a>
                                        @else
                                            <a href="{{route('countries.movement', $country->id)}}" class="btn btn-success btn-sm"><i class="fa fa-unlock" aria-hidden="true"></i> Activar</a>
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
