@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Stock réel vs légal')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
@endpush
@section('page')
    <div class="row">
        <div class="col">
            <div class="card-title justify-content-between align-items-center">
                <h2 >Stock réel vs légal</h2>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body ">
                    <div class="col-12">
                        <div>
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Référence</th>
                                    <th>Produit</th>
                                    <th>Stock réel</th>
                                    <th>Stock légal</th>
                                    <th>Ventes facturées</th>
                                    <th>Achats facturées</th>
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
            { data: 'quantite' },
            { data: 'stock_legal' },
            { data: 'stock_vente' },
            { data: 'stock_achat' },
        ];
        const __dataTable_ajax_link = "{{ route('rapports.stock-produit-legal') }}";
        const __dataTable_id = "#datatable";
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
@endpush

