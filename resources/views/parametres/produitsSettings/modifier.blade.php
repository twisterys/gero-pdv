@extends('layouts.main')
@section('document-title','Paramètres de produit')
@push('styles')
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{route('produits-settings.sauvegarder')}}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Paramètres de produit</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    <div class="row col-12 mx-0 ">
                        <div class="col-12">
                            <table class="table table-bordered table-striped mt-3 rounded overflow-hidden">
                                <tr>
                                    <th>Option</th>
                                    <th>Valeur</th>
                                </tr>
                                <tr>
                                    <td>Image</td>
                                    <td>
                                        <input name="image" value="1" type="checkbox" id="image"
                                               switch="bool" @checked(old('image',$produit_settings->where('key','image')->first()?->value)) >
                                        <label for="image" data-on-label="Oui" data-off-label="Non"></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Marque
                                    </td>
                                    <td>
                                        <input name="marque" value="1" type="checkbox" id="marque"
                                               switch="bool" @checked(old('marque',$produit_settings->where('key','marque')->first()?->value)) >
                                        <label for="marque" data-on-label="Oui" data-off-label="Non"></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Numéro de série
                                    </td>
                                    <td>
                                        <input name="numero_serie" value="1" type="checkbox" id="numero_serie"
                                               switch="bool" @checked(old('numero_serie',$produit_settings->where('key','numero_serie')->first()?->value)) >
                                        <label for="numero_serie" data-on-label="Oui" data-off-label="Non"></label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

@endpush

