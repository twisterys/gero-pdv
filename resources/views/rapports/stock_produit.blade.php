@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Rapport de stock')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
@endpush
@section('page')
        <div class="row">
            <div class="col">
                <div class="card-title justify-content-between align-items-center">
                    <h2 >Rapport de stock</h2>
                </div>
            </div>
        </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-md-nowrap flex-wrap">
                    <!-- Display the calculated values -->
                    <div class="card-title me-5   justify-content-between align-items-center">
                        <h5>Valeurs d'achats</h5>
                        <hr class="border">
                        <div id="total_achats" class="text-danger">
                            {{ $stock_achats }} DH
                        </div>
                    </div>
                    <div class="card-title me-5 justify-content-between align-items-center">
                        <h5>Valeurs de vente</h5>
                        <hr class="border">
                        <div id="total_ventes" class="text-success">
                            {{ $stock_ventes }} DH
                        </div>
                    </div>
                    <div class="card-title me-5 justify-content-between align-items-center">
                        <h5>Bénéfice potentiel</h5>
                        <hr class="border">
                        <div id="total_benefice" class="text-warning">
                            {{ $benifice }} DH
                        </div>
                    </div>
                    <div class="card-title justify-content-between align-items-center">
                        <h5>Profit Margin %</h5>
                        <hr class="border">
                        <div id="proft_margin" class="text-info">
                            {{ $profit }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body ">
                    <!-- Display the calculated values -->
                    <div class="card-title justify-content-between align-items-center">
                        <h4>Produits</h4>
                        <hr>
                    </div>
                    <div class="col-12">
                        <div>
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Référence</th>
                                    <th>Produit</th>
                                    <th>Prix unitaire</th>
                                    <th>Stock actuel</th>
                                    <th>Valeur achats</th>
                                    <th>Valeur ventes</th>
                                    <th>Bénéfice potentiel</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @include('layouts.partials.js.__datatable_js')

    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
                { data: 'reference' },
                { data: 'designation' },
                { data: 'prix_vente' },
                { data: 'quantite' },
                { data: 'valeur_achats' ,orderable: false,searchable: false },
                { data: 'valeur_ventes' ,orderable: false,searchable: false },
                { data: 'bénéfice_potentiel',orderable: false,searchable: false }
        ];
        const __dataTable_ajax_link = "{{ route('rapports.stock-produit') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            i_date: '#i_date',
            i_search: '#i_search',
            i_types: '#i_type'
        }
        const __dataTable_filter_trigger_button_id = '#i_search_button';
        $('#i_date').change(function () {
            table.ajax.reload();
        })
        $('#i_type').select2({
            minimumResultsForSearch:-1,
            multiple:!0,
        })
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
@endpush

