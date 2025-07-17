@extends('layouts.main')
@section('document-title','Inventaire')
@push('styles')
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('inventaire-liste') }}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3"><i
                                        class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>Inventaire
                                </h5>
                            </div>

                            <div class="pull-right">
                                @if($o_inventaire->type_inventaire !== "manuel")
                                    <a class="btn btn-soft-warning" href="{{$file}}" >
                                        <i class="fa fa-download" > Télécharger </i>
                                    </a>
                                @endif

                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="row py-3 px-1 mx-0 my-3 rounded">
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-id-card fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Reference</span>
                                <p class="mb-0 h5 text-black">{{$o_inventaire->reference}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-store fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Magasin</span>
                                <p class="mb-0 h5 text-black">{{$o_inventaire->magasin->nom}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-star fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Statut</span>
                                <p class="mb-0 h5 text-black">{{$o_inventaire->statut}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                            <div class="rounded bg-soft-danger  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-filter fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Type</span>
                                <p class="mb-0 h5 text-black">{{$o_inventaire->type}}</p>
                            </div>
                        </div>

                    </div>
                    <div class="row py-3 px-1 mx-0 my-3 rounded">
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fas fa-truck-loading fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Type d'inventaire</span>
                                <p class="mb-0 h5 text-black">
                                    {{ ucfirst($o_inventaire->type_inventaire ?? 'Automatique') }}
                                </p>
                            </div>
                        </div>


                    </div>
                   <div class="row">
                       <div class="col-12">
                           <h5 class="mt-4">Transactions</h5>
                           <hr>
                           <table class="table table-bordered table-striped">
                               <thead>
                               <tr>
                                   <th>Article</th>
                                   <th>Quantité entrée</th>
                                   <th>Quantité sortir</th>
                                   <th>Date</th>
                               </tr>
                               </thead>
                               <tbody>
                               @foreach($o_inventaire->transactions as $transaction)
                                   <tr>
                                       <td> ({{$transaction->article->reference}}) {{$transaction->article->designation}} </td>
                                       <td> {{$transaction->qte_entree}}</td>
                                       <td> {{$transaction->qte_sortir}}</td>
                                       <td> {{\Carbon\Carbon::make($transaction->date)->format('d/m/Y')}}</td>
                                   </tr>
                               @endforeach
                               </tbody>
                           </table>
                       </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
@endsection

