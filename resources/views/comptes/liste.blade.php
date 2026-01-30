@extends('layouts.main')
@section('document-title','comptes')
@push('styles')
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card ">
                <div class="card-body ">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div class="d-flex switch-filter justify-content-between align-items-center">
                            <h5 class="m-0"><i class="fa  fas fa-university me-2 text-success"></i>Mes comptes</h5>
                            <div class="page-title-right">
                                <a href="{{ route('comptes.ajouter') }}">
                                    <button class="btn btn-soft-info"><i class="mdi mdi-plus"></i> Ajouter</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- #####--DataTable--##### -->
                </div>
            </div>
        </div>
        <div class="col-12 d-flex">
            <div class="card w-100">

                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i class="mdi mdi-repeat me-2 text-success"></i> Banques
                            </h5>
                            <div class="page-title-right">
                                <a href="{{ route('releve-bancaire.liste') }}">
                                    <button class="btn btn-soft-success"><i class="fa fa-money-check"></i></i> Relevés bancaires</button>
                                </a>
                            </div>
                        </div>
                        <hr class="border">
                    <div class="row">
                        @foreach($banques as $banque)
                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <a href="{{route('comptes.afficher',$banque->id)}}">

                                    <div
                                        class="card  h-100 border-2 border d-flex flex-column align-items-center rounded overflow-hidden shadow-sm">
                                        <div
                                            class="mb-1 w-100 overflow-hidden d-flex align-items-center justify-content-center"
                                            style="max-height: 120px;">
                                            <img src="{{asset($banque->banque?->image)}}" class="img-fluid" alt="">
                                        </div>
                                        <div class="card-body text-center">
                                            <h3>{{$banque->nom}}</h3>
                                            <p class="m-0 text-center fw-bold my1 text-success">{{number_format($banque->solde,3,'.',' ')}}
                                                MAD </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <h5 class="m-0 mt-5">Caisses</h5>
                    <hr>
                    <div class="row">
                        @foreach($caisses as $caisse)
                            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                                <a href="{{route('comptes.afficher',$caisse->id)}}">

                                    <div
                                        class="card h-100 border-2 border d-flex flex-column align-items-center rounded overflow-hidden shadow-sm">
                                        <div
                                            class="article-card-header mb-1 w-100 overflow-hidden d-flex align-items-center"
                                            style="max-height: 120px;">
                                            <div class="bg-secondary-subtle w-100 p-4 text-center ">
                                                <div class="fa fa-cash-register fa-4x text-white"></div>
                                            </div>
                                        </div>
                                        <div class="card-body text-center">
                                            <h3>{{$caisse->nom}}</h3>
                                            <p class="m-0 text-center fw-bold my1 text-success">{{number_format($caisse->solde,3,'.',' ')}}
                                                MAD </p>
                                        </div>
                                    </div>
                                </a>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')

@endpush
