@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Error en la carga packing de Neppex</h3>
        </div>
        <div class="section-body">
            <div class="row">
                @include('sweetalert::alert')
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{route('neppex.lastrecord')}}">Reintentar carga...</a>
                            <br/>
                            <a href="{{route('neppex.index')}}">Pagina principal</a>
                            <br/>
                            <br/>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead style="background-color:#6777ef">
                                <tr>
                                    <th style="color:#fff;">Numero de caja</th>
                                    <th style="color:#fff;">Motivo</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($boxes as $box)
                                        @if($box['status'] == false)
                                            <tr class="table-danger">
                                                <td>{{$box['overallBox']}}</td>
                                                <td>{{$box['message']}}</td>
                                            </tr>
                                        @else
                                            <tr class="table-success">
                                                <td>{{$box['overallBox']}}</td>
                                                <td>{{$box['message']}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Numero de caja</th>
                                    <th>Motivo</th>
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
