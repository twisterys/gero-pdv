@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Rapport annuel arrêté')
@push('styles')
@endpush
@section('page')
    <div class="row">
        <div class="col-12 mb-4 row m-0 justify-content-between">
            <div class="col-md-6 col-12">
                <h2 class="m-0" >Rapport annuel arrêté</h2>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Display the calculated values -->
                    <div class="card-title">
                        <h4>Achats</h4>
                        <hr>
                    </div>
                    <div class="row w-100 align-items-center">
                        <div id="achat-container" class="position-relative">
                            @include('rapports.partials.annuel.achat')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body ">
                    <!-- Display the calculated values -->
                    <div class="card-title">
                        <h4 class="">Ventes </h4>
                        <hr>
                    </div>
                    <div class="row w-100 align-items-center">
                        <div id="vente-container" class="position-relative">
                            @include('rapports.partials.annuel.vente')
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body ">
                    <!-- Display the calculated values -->
                    <div class="card-title">
                        <h4 class="">Dépenses </h4>
                        <hr>
                    </div>
                    <div class="row w-100 align-items-center">
                        <div id="vente-container" class="position-relative">
                            @include('rapports.partials.annuel.depense')
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection

