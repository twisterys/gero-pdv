@extends('layouts.main')
@section('document-title','Paramètres de point de vente ')
@push('styles')
@endpush
@section('page')
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{route('pos-settings.sauvegarder')}}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Paramètres de point de vente</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="posSettingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">Général</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rapports-tab" data-bs-toggle="tab" data-bs-target="#rapports" type="button" role="tab" aria-controls="rapports" aria-selected="false">Rapports</button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="posSettingsTabsContent">
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row col-12 mx-0">
                                <div class="col-12">
                                    <table class="table table-bordered table-striped mt-3 rounded overflow-hidden">
                                        <tr>
                                            <th>Option</th>
                                            <th>Valeur</th>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','type_vente')->first()?->label}}</td>
                                            <td>
                                                <select name="type_vente" class="form-select" id="">
                                                    @foreach(\App\Models\Vente::TYPES as $type)
                                                        <option @selected(old('type_vente',$pos_settings->where('key','type_vente')->first()?->value) === $type) value="{{$type}}">@lang('ventes.'.$type)</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','type_retour')->first()?->label}}</td>
                                            <td>
                                                <select name="type_retour" class="form-select" id="">
                                                    @foreach(\App\Models\Vente::TYPES as $type)
                                                        <option @selected(old('type_retour',$pos_settings->where('key','type_retour')->first()?->value) === $type) value="{{$type}}">@lang('ventes.'.$type)</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','type_pos')->first()?->label}}</td>
                                            <td>
                                                <select name="type_pos" class="form-select" id="">
                                                    <option @selected(old('type_pos',$pos_settings->where('key','type_pos')->first()?->value) === 'parfums') value="parfums">Parfums</option>
                                                    <option @selected(old('type_pos',$pos_settings->where('key','type_pos')->first()?->value) === 'commercial') value="commercial">Commercial</option>
                                                    <option @selected(old('type_pos',$pos_settings->where('key','type_pos')->first()?->value) === 'classic') value="classic">Classic</option>
                                                    <option @selected(old('type_pos',$pos_settings->where('key','type_pos')->first()?->value) === 'caisse') value="caisse">Caisse</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','reduction')->first()?->label}}</td>
                                            <td>
                                                <input name="reduction" value="1" type="checkbox" id="reduction"
                                                       switch="bool" @checked(old('reduction',$pos_settings->where('key','reduction')->first()?->value)) >
                                                <label for="reduction" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','modifier_prix')->first()?->label}}</td>
                                            <td>
                                                <input name="modifier_prix" value="1" type="checkbox" id="modifier_prix"
                                                       switch="bool" @checked(old('modifier_prix',$pos_settings->where('key','modifier_prix')->first()?->value)) >
                                                <label for="modifier_prix" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','demandes')->first()?->label}}</td>
                                            <td>
                                                <input name="demandes" value="1" type="checkbox" id="demandes"
                                                       switch="bool" @checked(old('demandes',$pos_settings->where('key','demandes')->first()?->value)) >
                                                <label for="demandes" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','historique')->first()?->label}}</td>
                                            <td>
                                                <input name="historique" value="1" type="checkbox" id="historique"
                                                       switch="bool" @checked(old('historique',$pos_settings->where('key','historique')->first()?->value)) >
                                                <label for="historique" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','depenses')->first()?->label}}</td>
                                            <td>
                                                <input name="depenses" value="1" type="checkbox" id="depenses"
                                                       switch="bool" @checked(old('depenses',$pos_settings->where('key','depenses')->first()?->value)) >
                                                <label for="depenses" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','ticket')->first()?->label}}</td>
                                            <td>
                                                <input name="ticket" value="1" type="checkbox" id="ticket"
                                                       switch="bool" @checked(old('ticket',$pos_settings->where('key','ticket')->first()?->value))
                                                       onchange="if(!this.checked) document.getElementById('double_ticket_template').checked = false;" >
                                                <label for="ticket" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> Modéle de {{$pos_settings->where('key','double_ticket_template')->first()?->label}}</td>
                                            <td>
                                                <input name="double_ticket_template" value="1" type="checkbox" id="double_ticket_template"
                                                       switch="bool" @checked(old('double_ticket_template',$pos_settings->where('key','double_ticket_template')->first()?->value))
                                                       onchange="if(this.checked) document.getElementById('ticket').checked = true;" >
                                                <label for="double_ticket_template" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-12 d-flex justify-content-center align-items-center">
                                    <div style="max-width: 500px" >
                                        <textarea name="ticket_template" class="summernote" >{!! $pos_settings->where('key','ticket_template')->first()?->value !!}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rapports Tab -->
                        <div class="tab-pane fade" id="rapports" role="tabpanel" aria-labelledby="rapports-tab">
                            <div class="row col-12 mx-0">
                                <div class="col-12">
                                    <table class="table table-bordered table-striped mt-3 rounded overflow-hidden">
                                        <tr>
                                            <th>Rapport</th>
                                            <th>Activer/Désactiver</th>
                                        </tr>
                                        @foreach($rapports as $rapport)
                                        <tr>
                                            <td>{{ $rapport->nom }}</td>
                                            <td>
                                                <input name="rapport_{{ $rapport->cle }}" value="1" type="checkbox" id="rapport_{{ $rapport->cle }}"
                                                       switch="bool" @checked($rapport->actif) >
                                                <label for="rapport_{{ $rapport->cle }}" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('libs/tinymce/jquery.tinymce.min.js') }}"></script>
    <script src="{{ asset('libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: ".summernote",
            oninit: "setPlainText",
            height: 500,
            menubar: !1,
            plugins: __tinymce_plugins,
            toolbar: __tinymce_toolbar,
            toolbar_mode: "floating",
            noneditable_class: 'nonedit',
            content_style:
                "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
        });
    </script>
@endpush
