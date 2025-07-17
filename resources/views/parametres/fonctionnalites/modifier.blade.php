@extends('layouts.main')
@section('document-title','Fonctionnalités')
@push('styles')
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{route('fonctionnalites.sauvegarder')}}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Fonctionnalités</h5>
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
                                    <th>Fonctionnalité</th>
                                    <th>Active</th>
                                </tr>
                                <tr>
                                    <td>Modifier Références</td>
                                    <td>
                                        <input name="i_modifier_reference" value="1" type="checkbox" id="modifier_reference"
                                               switch="bool" @checked(old('i_modifier_reference',$o_global_settings->modifier_reference)) >
                                        <label for="modifier_reference" data-on-label="Oui" data-off-label="Non"></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Prix de revient</td>
                                    <td>
                                        <input name="i_prix_revient" value="1" type="checkbox" id="i_prix_revient"
                                               switch="bool" @checked(old('i_prix_revient',$o_global_settings->prix_revient)) >
                                        <label for="i_prix_revient" data-on-label="Oui" data-off-label="Non"></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Template par document</td>
                                    <td>
                                        <input name="i_template_par_document" value="1" type="checkbox" id="i_template_par_document"
                                               switch="bool" @checked(old('i_template_par_document',$o_global_settings->template_par_document)) >
                                        <label for="i_template_par_document" data-on-label="Oui" data-off-label="Non"></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Code barre</td>
                                    <td>
                                        <input name="i_code_barre" value="1" type="checkbox" id="i_code_barre"
                                               switch="bool" @checked(old('i_code_barre',$o_global_settings->code_barre)) >
                                        <label for="i_code_barre" data-on-label="Oui" data-off-label="Non"></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contrôle</td>
                                    <td>
                                        <input name="i_controle" value="1" type="checkbox" id="i_controle"
                                               switch="bool" @checked(old('i_controle',$o_global_settings->controle)) >
                                        <label for="i_controle" data-on-label="Oui" data-off-label="Non"></label>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Pièces jointes</td>
                                    <td>
                                        <input name="i_pieces_jointes" value="1" type="checkbox" id="pieces_jointes"
                                               switch="bool" @checked(old('i_pieces_jointes',$o_global_settings->pieces_jointes)) >
                                        <label for="pieces_jointes" data-on-label="Oui" data-off-label="Non"></label>
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

