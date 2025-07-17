@extends('layouts.main')
@section('document-title','Demandes de transfert')
@push('styles')
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div  class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0">
                                    <a href="{{route('transferts.afficher.demandes')}}"><i class="fa fa-arrow-left me-2"></i></a>
                                    <i class="fa  fas fa-boxes me-2 text-success"></i>
                                    Demandes de transferts</h5>

                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <div class="col-12 mb-4 p-0">
                        <div class="row m-0">
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                                <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-id-card fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Référence</span>
                                    <p class="mb-0 h5 text-black">{{$o_demande->reference??'-'}}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                                <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-store-alt fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Magasin d'entré</span>
                                    <p class="mb-0 h5 text-black">{{$o_demande->magasin_entree->nom??'-'}}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                                <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-store fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Magasin de sortie</span>
                                    <p class="mb-0 h5 text-black">{{$o_demande->magasin_sortie->nom??'-'}}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                                <div class="rounded bg-soft-danger  p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-clock fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Date</span>
                                    <p class="mb-0 h5 text-black">{{$o_demande->created_at->format('d/m/Y')??'-'}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="bordered table table-striped">
                        <thead>
                        <tr class="bg-primary" >
                            <th class="text-white" >Article</th>
                            <th class="text-white">Quantité demandée</th>
                            <th class="text-white">Quantité livrée</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($o_demande->lignes as $ligne)
                            <tr>
                                <td>{{$ligne->article->designation}}</td>
                                <td>{{$ligne->quantite_demande}}</td>
                                <td>{{$ligne->quantite_livre ?? '--' }} </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
