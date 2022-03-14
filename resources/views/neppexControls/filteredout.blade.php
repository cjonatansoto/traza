@extends('layouts.app')
@section('content')
    <script>
        $(".Books_Illustrations").select2("val", ["a", "c"]);
    </script>
    <section class="section">
        <div class="section-header">
            <div>
                <h3 class="page__heading">Filtar</h3>
                <ul style="background-color: #ffffff" class="breadcrumb breadcrumb-transparent breadcrumb-dot my-2 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('neppex.index') }}">Neppex
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="">Filtar</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                @include('sweetalert::alert')
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    {!! Form::open(array('route' => 'neppex.filteredoutstore','method'=>'POST')) !!}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Fecha carga inicio</label>
                                                <input type="date" class="form-control" name="start_date_load">
                                                @if ($errors->has('start_date_load'))
                                                    <div class="mt-3">
                                                         <span class="text-danger text-left mt-3">
                                                              {{ $errors->first('start_date_load') }}
                                                         </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Fecha carga fin</label>
                                                <input type="date" class="form-control" name="end_date_load">
                                                @if ($errors->has('end_date_load'))
                                                    <div class="mt-3">
                                                         <span class="text-danger text-left mt-3">
                                                              {{ $errors->first('end_date_load') }}
                                                         </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Fecha autorización inicio</label>
                                                <input type="date" class="form-control" name="start_date_authorization">
                                                @if ($errors->has('start_date_authorization'))
                                                    <div class="mt-3">
                                                         <span class="text-danger text-left mt-3">
                                                              {{ $errors->first('start_date_authorization') }}
                                                         </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Fecha autorización fin</label>
                                                <input type="date" class="form-control" name="end_date_authorization">
                                                @if ($errors->has('end_date_authorization'))
                                                    <div class="mt-3">
                                                         <span class="text-danger text-left mt-3">
                                                              {{ $errors->first('end_date_authorization') }}
                                                         </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Codaut</label>
                                                <input type="text" class="form-control" name="codaut">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Codigo de traspaso</label>
                                                <input type="text" class="form-control" name="transfer_code">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Pais destino</label>
                                                <select class="form-control select2" name="country_id">
                                                    <option value="" selected disabled hidden>SELECCIONAR...</option>
                                                    @foreach($countries as $country)
                                                        @if(old('country_id') == $country->id)
                                                            <option value="{{$country->id}}" selected>{{$country->name}}</option>
                                                        @endif
                                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('country_id'))
                                                    <div class="mt-3">
                                                         <span class="text-danger text-left mt-3">
                                                              {{ $errors->first('country_id') }}
                                                         </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Exportador</label>
                                                <select class="form-control select2" name="export_id">
                                                    <option value="" selected disabled hidden>SELECCIONAR...</option>
                                                    @foreach($exporters as $exporter)
                                                        @if(old('export_id') == $exporter->id)
                                                            <option value="{{$exporter->id}}" selected>{{$exporter->rut.'-'.$exporter->name}}</option>
                                                        @endif
                                                        <option value="{{$exporter->id}}">{{$exporter->rut.'-'.$exporter->name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('export_id'))
                                                    <div class="mt-3">
                                                         <span class="text-danger text-left mt-3">
                                                              {{ $errors->first('export_id') }}
                                                         </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
