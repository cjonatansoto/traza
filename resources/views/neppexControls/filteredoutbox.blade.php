@extends('layouts.app')
@section('content')
    <script>
        $(".Books_Illustrations").select2("val", ["a", "c"]);
    </script>
    <section class="section">
        <div class="section-header">
            <div>
                <h3 class="page__heading">Filtar cajas</h3>
                <ul style="background-color: #ffffff" class="breadcrumb breadcrumb-transparent breadcrumb-dot my-2 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('neppex.index') }}">Neppex
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="">Filtar cajas</a>
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
                                    {!! Form::open(array('route' => 'neppex.filteredoutboxstore','method'=>'POST')) !!}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Numero de caja general</label>
                                                <input type="number" class="form-control" name="overall_box">
                                                @if ($errors->has('overall_box'))
                                                    <div class="mt-3">
                                                         <span class="text-danger text-left mt-3">
                                                              {{ $errors->first('overall_box') }}
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
