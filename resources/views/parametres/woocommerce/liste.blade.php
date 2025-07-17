@extends('layouts.main')

@section('document-title', 'Liste des paramètres WooCommerce');
@push('styles')
    <style>
        table {
            font-size: unset;
        }
    </style>
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" id="woocommerce-form"
                      action="{{ route('woocommerce.mettre_a_jour') }}" class="needs-validation"
                      novalidate autocomplete="off">
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('parametres.liste') }}"><i
                                        class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Configuration Woocommerce</h5>
                            </div>
                            <div class="pull-right">
                                <button id="test-btn" data-url="{{route('woocommerce.testConnection')}}" class="btn btn-soft-warning mx-2"><i class="fa fa-save"></i> Tester
                                </button>
                                <button type="submit" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder
                                </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    <div class="row col-12 mx-0 g-3">
                        <div class="col-md-4 col-sm-6">
                            <label for="type-input" class="form-label required">Lien de votre magasin</label>
                            <input type="text" name="store_url"
                                   class="form-control"
                                   value="{{ old('store_url', $parametres?->store_url ?? '') }}" required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label for="type-input" class="form-label required">Clé consommateur</label>
                            <input type="text" name="consumer_key"
                                   class="form-control"
                                   value="{{ old('consumer_key', $parametres?->consumer_key ?? '') }}" required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label for="consumer_secret-input" class="form-label required">Clé secrète</label>
                            <input type="text" name="consumer_secret" id="consumer_secret-input"
                                   class="form-control"
                                   value="{{ old('consumer_secret', $parametres?->consumer_secret ?? '') }}"
                                   required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label for="version-input" class="form-label">Version</label>
                            <input type="text" name="version" id="version-input"
                                   class="form-control"
                                   value="{{ old('consumer_key', $parametres?->version ?? 'v3') }}" required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label for="price_value-input" class="form-label">Prix d'article</label>
                            <select name="price_value" id="price_value-input" class="form-select" >
                                <option value="regular_price" {{$parametres?->price_value  === 'regular_price'  ? "selected" : ''}} >Prix régulier</option>
                                <option value="sale_price" {{$parametres?->price_value === 'sale_price' ? "selected" : ''}}>Prix de solde</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/parametres/woocommerce_liste.js'])
@endpush
