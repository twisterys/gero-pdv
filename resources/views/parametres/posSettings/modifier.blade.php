@extends('layouts.main')
@section('document-title','Paramètres de point de vente ')
@push('styles')
<style>
    .nav-tabs .nav-link {
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-bottom-color: #f8f9fa;
        box-shadow: 0 -2px 5px rgba(0,0,0,0.05);
    }
    .nav-tabs .nav-link:hover:not(.active) {
        background-color: rgba(0,0,0,0.03);
    }
    .settings-table th {
        background-color: #f8f9fa;
    }
    .settings-table td {
        vertical-align: middle;
    }
    .floating-save-btn {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 1000;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 500;
        margin: 1.5rem 0 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
        color: #495057;
    }
    .template-editor-container {
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 1.5rem;
        background-color: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-top: 1.5rem;
    }
    .template-instructions {
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .template-instructions h5 {
        font-weight: 600;
    }
    .template-instructions code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.9rem;
    }
    .template-instructions table {
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
</style>
@endpush
@section('page')
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{route('pos-settings.sauvegarder')}}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title mb-4">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{route('parametres.liste')}}" class="btn btn-sm btn-outline-secondary me-2"><i class="fa fa-arrow-left text-success me-1"></i> Retour</a>
                                <h4 class="m-0">Paramètres de point de vente</h4>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-primary"><i class="fa fa-save me-1"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    <!-- Floating Save Button -->
                    <button type="submit" class="btn btn-primary floating-save-btn" title="Sauvegarder">
                        <i class="fa fa-save"></i>
                    </button>

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-3" id="posSettingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                <i class="fa fa-cog me-1"></i> Général
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rapports-tab" data-bs-toggle="tab" data-bs-target="#rapports" type="button" role="tab" aria-controls="rapports" aria-selected="false">
                                <i class="fa fa-file-alt me-1"></i> Rapports
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="posSettingsTabsContent">
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row col-12 mx-0">
                                <div class="col-12">
                                    <table class="table table-bordered table-hover settings-table mt-3 rounded overflow-hidden">
                                        <tbody>
                                        <!-- Types de vente -->
                                        <tr id="types_vente" class="table-active">
                                            <th colspan="2"><i class="fa fa-tags me-2"></i>Types de vente</th>
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

                                        <!-- Fonctionnalités -->
                                        <tr id="fonctionnalites" class="table-active">
                                            <th colspan="2"><i class="fa fa-sliders-h me-2"></i>Fonctionnalités</th>
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
                                            <td>{{$pos_settings->where('key','global_reduction')->first()?->label ?? 'Réduction globale'}}</td>
                                            <td>
                                                <input name="global_reduction" value="1" type="checkbox" id="global_reduction"
                                                       switch="bool" @checked(old('global_reduction',$pos_settings->where('key','global_reduction')->first()?->value)) >
                                                <label for="global_reduction" data-on-label="Oui" data-off-label="Non"></label>
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
                                            <td>{{$pos_settings->where('key','rebut')->first()?->label}}</td>
                                            <td>
                                                <input name="rebut" value="1" type="checkbox" id="rebut"
                                                       switch="bool" @checked(old('rebuts',$pos_settings->where('key','rebut')->first()?->value)) >
                                                <label for="rebut" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','cloture')->first()?->label ?? 'Clôture de caisse'}}</td>
                                            <td>
                                                <input name="cloture" value="1" type="checkbox" id="cloture"
                                                       switch="bool" @checked(old('cloture',$pos_settings->where('key','cloture')->first()?->value)) >
                                                <label for="cloture" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>



                                        <!-- Raccourcis de paiement -->
                                        <tr id="paiements" class="table-active">
                                            <th colspan="2"><i class="fa fa-money-bill-wave me-2"></i>Raccourcis de paiement</th>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','button_credit')->first()?->label ?? 'Bouton Crédit'}}</td>
                                            <td>
                                                <input name="button_credit" value="1" type="checkbox" id="button_credit"
                                                       switch="bool" @checked(old('button_credit',$pos_settings->where('key','button_credit')->first()?->value)) >
                                                <label for="button_credit" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','button_other')->first()?->label ?? 'Bouton Autre'}}</td>
                                            <td>
                                                <input name="button_other" value="1" type="checkbox" id="button_other"
                                                       switch="bool" @checked(old('button_other',$pos_settings->where('key','button_other')->first()?->value)) >
                                                <label for="button_other" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{$pos_settings->where('key','button_cash')->first()?->label ?? 'Bouton Espèces'}}</td>
                                            <td>
                                                <input name="button_cash" value="1" type="checkbox" id="button_cash"
                                                       switch="bool" @checked(old('button_cash',$pos_settings->where('key','button_cash')->first()?->value)) >
                                                <label for="button_cash" data-on-label="Oui" data-off-label="Non"></label>
                                            </td>
                                        </tr>

                                        <!-- Tickets & Impression -->
                                        <tr id="tickets" class="table-active">
                                            <th colspan="2"><i class="fa fa-receipt me-2"></i>Tickets & Impression</th>
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
                                            <td>{{$pos_settings->where('key','autoTicketPrinting')->first()?->label ?? 'Impression automatique du ticket'}}</td>
                                            <td>
                                                <input name="autoTicketPrinting" value="1" type="checkbox" id="autoTicketPrinting"
                                                       switch="bool" @checked(old('autoTicketPrinting',$pos_settings->where('key','autoTicketPrinting')->first()?->value)) >
                                                <label for="autoTicketPrinting" data-on-label="Oui" data-off-label="Non"></label>
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
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Ticket Template Section -->
                                <div class="settings-section">
                                    <h4 class="section-title">
                                        <i class="fa fa-receipt me-2 text-success "></i>Modèle de Ticket
                                    </h4>

                                    <div class="row">
                                        <!-- Template Reference -->
                                        <div class="col-md-5">
                                            <div class="card border h-100">
                                                <div class="card-header bg-light">
                                                    <h6 class="m-0 font-weight-bold">Référence du modèle</h6>
                                                    <small class="text-muted">Variables disponibles pour le modèle</small>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered table-reference table-striped "
                                                               style="border-collapse: collapse !important;">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-white bg-primary">Clé</th>
                                                                <th class="text-white bg-primary">Signification</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td><code>[Client]</code></td>
                                                                <td>Nom de client</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Date_et_heure]</code></td>
                                                                <td>Date et heure d'impression</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Tableau]</code></td>
                                                                <td>Tableau des articles</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Reference]</code></td>
                                                                <td>Référence du document de vente</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Magasin_adresse]</code></td>
                                                                <td>Adresse de magasin</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Magasin]</code></td>
                                                                <td>Nom de magasin</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Total_HT]</code></td>
                                                                <td>Le total HT de vente</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Total_TVA]</code></td>
                                                                <td>Le total TVA de vente</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Total_TTC]</code></td>
                                                                <td>Le total TTC de vente</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Montant_Paye]</code></td>
                                                                <td>Le montant payé</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Montant_Restant]</code></td>
                                                                <td>Le montant resté à payer</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>[Nom_Revendeur]</code></td>
                                                                <td>Nom du revendeur (utilisateur authentifié)</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Template Editor -->
                                        <div class="col-md-7">
                                            <div class="card border h-100">
                                                <div class="card-header bg-light">
                                                    <h6 class="m-0 font-weight-bold">Éditeur de modèle</h6>
                                                    <small class="text-muted">Personnalisez le format de votre ticket</small>
                                                </div>
                                                <div class="card-body">
                                                    <textarea name="ticket_template"
                                                              class="summernote">{!! $pos_settings->where('key','ticket_template')->first()?->value !!}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rapports Tab -->
                        <div class="tab-pane fade" id="rapports" role="tabpanel" aria-labelledby="rapports-tab">
                            <div class="row col-12 mx-0">
                                <div class="col-12">
                                    <h5 class="section-title"><i class="fa fa-chart-bar me-2"></i>Configuration des Rapports</h5>
                                    <div class="card shadow-sm">
                                        <div class="card-body p-0">
                                            <table class="table table-bordered table-hover settings-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-light" style="width: 60%">Rapport</th>
                                                        <th class="bg-light">Activer/Désactiver</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
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
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
