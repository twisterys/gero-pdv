@extends('layouts.main')
@section('document-title','Mise en page PDF')
@push('styles')
    <link rel="stylesheet" href="{{asset('libs/filepond/plugins/css/filepond-plugin-image-preview.css')}}">
    <link rel="stylesheet" href="{{asset('libs/filepond/css/filepond.min.css')}}">
    <link rel="stylesheet" href="{{asset('libs/spectrum-colorpicker2/spectrum.min.css')}}">
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{ route('documents.mettre_a_jour', $o_document_parametres->id) }}"
                      class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items">
                                <a href="{{route('parametres.liste')}}"><i
                                        class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Mise en page PDF</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder
                                </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    @method('PUT')
                    <div class=" px-4 align-items-start">
                        <div class="row mb-4">
                            <div class="col-6">
                                <label for="template-input" class="form-label">Modèle</label>
                                <div class="input-group flex-nowrap">
                                    <select name="template" class="form-control" id="template-input">
                                        @foreach($templates as $template)
                                            <option
                                                @selected(old('template',$o_document_parametres->template_id) == $template->id) value="{{$template->id}}">{{$template->nom}}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="preview-btn" class="btn btn-primary"><i
                                            class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center align-content-center justify-content-center"
                             id="template-settings-content">
                            @if(in_array('logo',explode(',',$o_document_parametres->template->elements)))
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="filepond-input" name="logo" id="logo-input"
                                           data-max-width="{{$o_document_parametres->template->logo_largeur}}"
                                           data-max-height="{{$o_document_parametres->template->logo_hauteur}}"
                                           data-nom="logo"
                                           @if($o_document_parametres->template->logo) data-file="{{$o_document_parametres->template->logo}}" @endif>
                                </div>
                            @endif
                            @if(in_array('image_en_tete',explode(',',$o_document_parametres->template->elements)))
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="filepond-input" name="image_en_tete"
                                           id="image-en-tete-input"
                                           data-max-width="{{$o_document_parametres->template->image_en_tete_largeur}}"
                                           data-max-height="{{$o_document_parametres->template->image_en_tete_hauteur}}"
                                           data-nom="entête"

                                           @if($o_document_parametres->template->image_en_tete) data-file="{{ $o_document_parametres->template->image_en_tete}}" @endif>
                                </div>
                            @endif
                            @if(in_array('image_en_bas',explode(',',$o_document_parametres->template->elements)))
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="filepond-input" name="image_en_bas"
                                           id="image-en-bas-input"
                                           data-max-width="{{$o_document_parametres->template->image_en_bas_largeur}}"
                                           data-max-height="{{$o_document_parametres->template->image_en_bas_hauteur}}"
                                           data-nom="bas de page"
                                           @if($o_document_parametres->template->image_en_bas) data-file="{{$o_document_parametres->template->image_en_bas }}" @endif>
                                </div>
                            @endif
                            @if(in_array('image_arriere_plan',explode(',',$o_document_parametres->template->elements)))
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="filepond-input" name="image_arriere_plan"
                                           id="image-arriere-plan-input"
                                           data-max-width="794"
                                           data-max-height="1123" data-nom="arrière-plan"
                                           @if($o_document_parametres->template->image_arriere_plan) data-file="{{$o_document_parametres->template->image_arriere_plan}}" @endif>
                                </div>
                            @endif
                            @if(in_array('cachet',explode(',',$o_document_parametres->template->elements)))
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="filepond-input" name="cachet" id="cachet-input"
                                               data-max-width="170"
                                               data-max-height="170"
                                               data-nom="cachet"
                                               @if($o_document_parametres->template->cachet) data-file="{{$o_document_parametres->template->cachet}}" @endif>
                                    </div>

                                @endif
                            <div class="col-md-6 mb-3">
                                <label for="coleur-input">Couleur</label>
                                <input type="text" name="couleur"
                                       value="{{old('couleur',$o_document_parametres->template->couleur)}}"
                                       class="form-control"
                                       id="coleur-input">
                            </div>
                            <div class="col-md-6 mb-md-0 mb-3 d-flex align-items-center justify-content-between">
                                <label class="form-label  me-3 " for="afficher_total_en_chiffre">Afficher le total en
                                    chiffre</label>
                                <input name="afficher_total_en_chiffre" value="1" type="checkbox"
                                       id="afficher_total_en_chiffre"
                                       switch="bool" @checked(old('afficher_total_en_chiffre', $o_document_parametres->template->afficher_total_en_chiffre))>
                                <label for="afficher_total_en_chiffre" data-on-label="Oui" data-off-label="Non"></label>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{asset('libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
    <script src="{{asset('libs/filepond/plugins/js/filepond-plugin-image-preview.js')}}"></script>
    <script src="{{asset('libs/filepond/plugins/js/filepond-plugin-file-validate-type.js')}}"></script>
    <script src="{{asset('libs/filepond/plugins/js/filepond-plugin-image-validate-size.js')}}"></script>
    <script src="{{asset('libs/filepond/js/filepond.min.js')}}"></script>
    <script src="{{asset('libs/filepond/js/filepond.jquery.min.js')}}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script type="module">
        FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.registerPlugin(FilePondPluginFileValidateType);
        FilePond.registerPlugin(FilePondPluginImageValidateSize);
    </script>
    <script>
        const __template_card_url = '{{route('documents.template')}}/';
        const __image_load = '{{route('documents.load')}}/';
        const __document_show_url = '{{url('parametres/documents/voir/')}}/';
    </script>
    @vite('resources/js/documents_parameters.js')
@endpush
