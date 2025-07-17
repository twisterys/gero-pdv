@extends('layouts.main')

@section('document-title', 'Importation Woocommerce')

@push('styles')
    @include('layouts.partials.css.__datatable_css')
@endpush

@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('importer-liste') }}"><i
                                    class="fa fa-arrow-left text-success me-2"></i></a>
                            <h5 class="m-0">Importation Woocommerce</h5>
                        </div>
                       <div class="d-flex align-items-center g-2 pull-right">
                               <button data-url="{{route('woocommerce.importer-produits')}}"
                                       class="btn btn-soft-primary mx-1" id="products-import">
                                   <i class="fa  fas fa-barcode"></i>

                                   Produits </button>
                               <button data-bs-target="#import-orders" data-bs-toggle="modal"
                                       class="btn btn-soft-primary mx-1" id="products-import">
                                   <i class="mdi mdi-chart-bell-curve-cumulative"></i>

                                   Ventes </button>
                       </div>
                    </div>
                    <hr class="border">
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="width: 20px">
                                <input type="checkbox" class="form-check-input" id="select-all-row">
                            </th>
                            <th style="max-width: 150px">Référence</th>
                            <th>Type</th>
                            <th>Magasin</th>
                            <th>Statut</th>
                        </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="import-orders" tabindex="-1" aria-labelledby="add-cat-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-cat-modal-title">Importer les ventes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="orders-form" method="post" action="{{route('woocommerce.importer-ventes')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required " for="type-input">Type de vente</label>
                                <select name="type" class="form-select" id="">
                                    @foreach(\App\Models\Vente::TYPES as $type)
                                        <option @selected(old('type_vente') === $type) value="{{$type}}">@lang('ventes.'.$type)</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(count($magasins) > 1)
                                <div class="col-12 mb-3">
                                    <label for="magasin_id-input" class="form-label required">Magasin</label>
                                    <select name="magasin_id" id="magasin-select">
                                        @foreach ($magasins as $magasin)
                                            <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Importer</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection

@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference', name: 'reference'},
            {data: 'type', name: 'type'},
            {data: 'magasin_id', name: 'magasin_id'},
            {data: 'statut', name: 'statut'},
        ];
        const __dataTable_ajax_link = "{{ route('woocommerce.import.liste') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            client_id: '#client-select',
            commercial_id: '#commercial-select',
            livraison_id: '#livraison-select',
            date_emission: '#date_emission',
            statut: '#statut-select',
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
    @vite(['resources/js/importations/woocommerce.js'])
@endpush
