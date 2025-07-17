@extends('layouts.main')
@section('document-title')
    Paramètres-@yield('document-title')
@endsection
@push('styles')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
    <link href="{{asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('libs/daterangepicker/css/daterangepicker.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
@endpush
@section('page')
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div  class="d-flex switch-filter justify-content-between align-items-center">
                            <h5 class="m-0"> <i class="mdi mdi-cog me-2 text-success" ></i> Paramètres </h5>
                        </div>
                        <hr class="border">
                    </div>
                    <div id="sidebar-menu" style="padding: 0">
                        <ul class="metismenu list-unstyled" id="side-menu">
                            @include('parametres.partials.__menu')
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-body" id="content-area">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
