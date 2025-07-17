@extends('layouts.main')

@section('document-title',__('Exportation'))
@push('styles')
@endpush
@section('page')

    <div class="row" style=" height: 70vh; overflow: hidden;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div  class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0 float-end">
                                    <i class="mdi me-2 text-success mdi-link"></i>
                                    Liens d'exportations
                                </h5>
                            </div>
                            <div class="pull-right">
                                <div class="btn-group">

                                    <a href="{{route('exporter-client')}}" class="btn btn-soft-primary mx-1">
                                        <i class="mdi mdi-account-group"></i> Clients
                                    </a>
                                </div>
                                <div class="btn-group">

                                    <a href="{{route('exporter-fournisseur')}}" class="btn btn-soft-primary mx-1">
                                        <i class="mdi mdi-truck"></i> Fournisseurs
                                    </a>
                                </div>
                                <div class="btn-group">
                                    <a href="{{route('exporter-produit')}}" class="btn btn-soft-primary mx-1">
                                        <i class="fa  fas fa-barcode"></i>
                                        Produits
                                    </a>
                                </div>
                                @if(\App\Services\LimiteService::is_enabled('stock'))

                                <div class="btn-group">

                                    <a href="{{route('exporter-stock-page')}}" class="btn btn-soft-primary mx-1">
                                        <i class="fa fas fa-boxes"></i>
                                        Stocks
                                    </a>
                                </div>
                                @endif
                                <div class="btn-group">

                                    <a href="{{route('exporter-vente-page')}}" class="btn btn-soft-primary mx-1">
                                        <i class="mdi mdi-chart-bell-curve-cumulative"></i>
                                        Ventes
                                    </a>
                                </div>
                                <div class="btn-group">

                                    <a href="{{route('exporter-achat-page')}}" class="btn btn-soft-primary mx-1">
                                        <i class="mdi mdi-shopping"></i> Achats
                                    </a>
                                </div>

                                <div class="btn-group">

                                    <a href="{{route('exporter-paiement-page')}}" class="btn btn-soft-primary mx-1">
                                        <i class="fa fas fa-money-bill"></i> Paiements
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            const dropdownItems2 = document.querySelectorAll('.item2');

            dropdownItems.forEach(item => {
                item.addEventListener('click', function (event) {
                    event.preventDefault();
                    const type = this.getAttribute('data-type');
                    const url = "{{ route('exporter-vente', ['type' => '']) }}/" + type;
                    window.location.href = url;
                });
            });
            dropdownItems2.forEach(item => {
                item.addEventListener('click', function (event) {
                    event.preventDefault();
                    const type = this.getAttribute('data-type');
                    const url = "{{ route('exporter-paiement', ['type' => '']) }}/" + type;
                    window.location.href = url;
                });
            });
        });
    </script>

@endpush
