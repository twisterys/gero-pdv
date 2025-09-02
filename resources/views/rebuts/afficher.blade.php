@extends('layouts.main')
@section('document-title','Rebut')
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
                                <a href="{{ route('rebuts.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3"><i
                                        class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>Rebut
                                </h5>
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
                                <p class="mb-0 h5 text-black">{{$o_rebut->reference}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-store fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Magasin</span>
                                <p class="mb-0 h5 text-black">{{$o_rebut->magasin->nom}}</p>
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
                                    <th>Quantité de rebut</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($o_rebut->transactions as $transaction)
                                    <tr>
                                        <td> ({{$transaction->article->reference}}) {{$transaction->article->designation}} </td>
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

