@extends('layouts.main')
@section('document-title', 'comptes')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('comptes.mettre_a_jour', $o_compte->id) }}" class="needs-validation" novalidate autocomplete="off">
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <h5 class="m-0"> <i class="fa  fas fa-cash-register me-2 text-success" ></i> Modifier un compte</h5>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" > Sauvegarder</span> </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                        @csrf
                        @method('PUT')
                        <div class="row row px-3 align-items-center" style="align-items: flex-start">
                            <div class="row col-md-12">
                                <div class="col-md-3 col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="nom">Nom de compte</label>
                                    <input required type="text" class="form-control {{$errors->has('nom') ? 'is-invalid' : null}}" id="nom" name="nom" placeholder="" minlength="3" value="{{old('nom',$o_compte->nom)}}">
                                    @if($errors->has('nom'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nom') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-3 col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="type">Type</label>
                                    <select required class="select2 form-control mb-3 custom-select {{$errors->has('type') ? 'is-invalid' : null}}" id="type-select" name="type">
                                        <option value="caisse" {{ old('type',$o_compte->type) === 'caisse' ? 'selected' : '' }}>Caisse</option>
                                        <option value="banque" {{ old('type',$o_compte->type) === 'banque' ? 'selected' : '' }}>Banque</option>
                                    </select>
                                    @if($errors->has('type'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('type') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-3 col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="statut-select">Statut</label>
                                    <select required class="select2 form-control mb-3 custom-select {{$errors->has('statut') ? 'is-invalid' : null}}" id="statut-select" name="statut">
                                        <option @if(old('statut',$o_compte->statut) == '0') selected @endif  value="0">Personnel</option>
                                        <option @if(old('statut',$o_compte->statut ) == '1') selected @endif value="1">Professionnel</option>
                                    </select>
                                    @if($errors->has('statut'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('statut') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-3 col-sm-6 col-12 mb-3" style="display: flex;align-items: flex-end">
                                    <div class="form-check-inline d-flex align-items-center">
                                        <label for="principal" class="form-check-label me-2" >Principal</label>
                                        <input value="1" name="principal" type="checkbox" id="principal" switch="bool" {{ $o_compte->principal ? 'checked' : '' }}>
                                        <label for="principal" data-on-label="Oui" data-off-label="Non"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-md-12 banque-fields" style="display: none;">
                                <div class="col-md-3 col-sm-6 col-12 mb-3" >
                                    <label class="form-label" for="banque">Banque</label>
                                    <select name="banque" id="banque" class="form-select @error('banque') is-invalid @enderror" >
                                        @foreach($banques as $banque)
                                            <option value="{{$banque->id}}" @selected(old('banque', $o_compte->banque_id) == $banque->id) data-img="{{asset($banque->image)}}">
                                                {{$banque->nom}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('banque'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('banque') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-3 col-sm-6 col-12 mb-3">
                                    <label class="form-label " for="adresse">Adresse</label>
                                    <input type="text" class="form-control {{$errors->has('adresse') ? 'is-invalid' : null}}" id="adresse" name="adresse" placeholder="" minlength="3" value="{{old('adresse',$o_compte->adresse)}}">
                                    @if($errors->has('adresse'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('adresse') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-3 col-sm-6 col-12 mb-3">
                                    <label class="form-label " for="rib">RIB</label>
                                    <input type="text" class="form-control input-mask {{$errors->has('rib') ? 'is-invalid' : null}}" id="rib" name="rib" im-insert="true"  data-inputmask="'mask': '999 999 9999999999999999 99'"
                                           placeholder="XXX XXX XXXXXXXXXXXXXXXX XX" minlength="24" maxlength="27" value="{{old('rib',$o_compte->rib)}}">
                                    @if($errors->has('rib'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('rib') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script src="{{asset('libs/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
    <script>
        $('.input-mask').inputmask()
        document.addEventListener("DOMContentLoaded", function() {
            $('select[name="type"]').on('change', function() {
                if ($(this).val() === 'banque') {
                    $('.banque-fields').show();
                    $('#banque, #adresse, #rib').prop('required', true);
                } else {
                    $('.banque-fields').hide();
                    $('#banque, #adresse, #rib').prop('required', false);
                    $('#banque, #adresse, #rib').val('');
                    $('#principal').prop('checked', false);
                }
            });
        });

        if ($('select[name="type"]').val() === 'banque') {
            $('.banque-fields').show();
            $('#banque, #adresse, #rib').prop('required', true);
        }
        // $('#rib').mask('000 000 0000000000000000 00')
    </script>
    <script>
        $('.select2').select2({
            width: '100%',
            placeholder: 'Selectioner un type',
            minimumResultsForSearch: -1
        })
        function formatState (state) {
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span><img style="max-width: 90px" src="'+ state.element.dataset.img +'" class="img-flag" /> ' + state.text + '</span>'
            );
            return $state;
        };
        $("#banque").select2({
            width:'100%',
            templateResult: formatState
        });
    </script>
@endpush
