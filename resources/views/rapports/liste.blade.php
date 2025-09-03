@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Rapports')
@push('styles')
@endpush
@section('page')
    <div class="row">
        <div class="col">
            <div class="card-title justify-content-between align-items-center">
                <h2><i class="fa fa-chart-pie me-2 text-success"></i> Rapports</h2>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            @if($achat_vente->count() > 0)
                <div class="card-title">
                    <h4>Achats et vente</h4>
                    <hr>
                </div>
            @endif
            <div class="row">
                @foreach($achat_vente as $rapport)
                    <div class="col-md-4 col-sm-6 col-12 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <div class="card-title m-0 justify-content-between align-items-center">
                                    <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                </div>
                            </div>
                            <div class="card-body ">
                                <p class="m-0">
                                    {{$rapport->description}}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
           @if($stock->count() > 0)
                <div class="card-title mt-2">
                    <h4>Stock</h4>
                    <hr>
                </div>
           @endif
            <div class="row">
                @foreach($stock as $rapport)
                    <div class="col-md-4 col-sm-6 col-12 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <div class="card-title m-0 justify-content-between align-items-center">
                                    <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                </div>
                            </div>
                            <div class="card-body ">
                                <p class="m-0">
                                    {{$rapport->description}}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
           @if($statistiques->count() > 0)
                <div class="card-title mt-2">
                    <h4>Statistiques</h4>
                    <hr>
                </div>
            @endif
            <div class="row">
                @foreach($statistiques as $rapport)
                    <div class="col-md-4 col-sm-6 col-12 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <div class="card-title m-0 justify-content-between align-items-center">
                                    <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                </div>
                            </div>
                            <div class="card-body ">
                                <p class="m-0">
                                    {{$rapport->description}}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

                @if($comptabilité->count() > 0)
                    <div class="card-title mt-2">
                        <h4>Comptabilité</h4>
                        <hr>
                    </div>
                @endif
                <div class="row">
                    @foreach($comptabilité as $rapport)
                        <div class="col-md-4 col-sm-6 col-12 mt-3">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <div class="card-title m-0 justify-content-between align-items-center">
                                        <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                    </div>
                                </div>
                                <div class="card-body ">
                                    <p class="m-0">
                                        {{$rapport->description}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($pos->count() > 0)
                    <div class="card-title mt-2">
                        <h4>Point de vente</h4>
                        <hr>
                    </div>
                @endif
                <div class="row">
                    @foreach($pos as $rapport)
                        <div class="col-md-4 col-sm-6 col-12 mt-3">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <div class="card-title m-0 justify-content-between align-items-center">
                                        <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                    </div>
                                </div>
                                <div class="card-body ">
                                    <p class="m-0">
                                        {{$rapport->description}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>
    </div>

@endsection

@push('scripts')

@endpush

