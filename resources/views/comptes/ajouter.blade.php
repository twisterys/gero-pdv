@extends('layouts.main')
@section('document-title','comptes')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
    <link href="{{asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('libs/daterangepicker/css/daterangepicker.min.css')}}" rel="stylesheet">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{route('comptes.sauvegarder') }}" class="needs-validation" novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div>
                                <a href="{{route('comptes.liste')}}"><i class="fa fa-arrow-left" ></i></a>
                                <h5 class="m-0 float-end ms-3"> <i class="fa  fas fa-cash-register me-2 text-success" ></i> Ajouter un compte</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" > Sauvegarder</span> </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                        @csrf
                        <div class="row px-3 align-items-center">
                            <div class="row col-md-12">
                                <div class="col-md-3 col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="nom">Nom de compte</label>
                                    <input required type="text" value="{{old('nom')}}" class="form-control {{$errors->has('nom') ? 'is-invalid' : null}} " id="nom" name="nom" placeholder="" minlength="3">
                                    @if($errors->has('nom'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nom') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-3 col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="type">Type</label>
                                    <select required class="select2 form-control  custom-select {{$errors->has('type') ? 'is-invalid' : null}}" id="type-select" name="type">
                                        <option @if(old('type') === 'caisse') selected @endif  value="caisse">Caisse</option>
                                        <option @if(old('type') === 'banque') selected @endif value="banque">Banque</option>
                                    </select>
                                    @if($errors->has('type'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('type') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-3 col-sm-6 col-12 mb-3" style="display: flex;align-items: flex-end">
                                    <div class="form-check-inline d-flex align-items-center">
                                        <label for="principal" class="form-check-label me-2" >Principal</label>
                                        <input value="1" name="principal" type="checkbox" id="principal" switch="bool">
                                        <label for="principal" data-on-label="Oui" data-off-label="Non"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row col-md-12 banque-fields" style="display: none;">
                                <div class="col-md-3 col-sm-6 col-12 mb-3" >
                                    <label class="form-label" for="banque">Banque</label>
                                    <select name="banque" id="banque" class="form-select @error('banque') is-invalid @enderror" >
                                       @foreach($banques as $banque)
                                            <option value="{{$banque->id}}" data-img="{{asset($banque->image)}}">
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
                                    <input type="text" class="form-control {{$errors->has('adresse') ? 'is-invalid' : null}}" id="adresse" value="{{old('adresse')}}" name="adresse" placeholder="" minlength="3">
                                    @if($errors->has('adresse'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('adresse') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-3 col-sm-6 col-12 mb-3">
                                    <label class="form-label " for="rib">RIB</label>
                                    <input type="text" class="form-control {{$errors->has('rib') ? 'is-invalid' : null}}" value="{{old('rib')}}" id="rib" name="rib"  im-insert="true"  data-inputmask="'mask': '999 999 9999999999999999 99'" placeholder="XXX XXX XXXXXXXXXXXXXXXX XX" minlength="24" maxlength="27">
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
    <script src="{{asset('libs/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js')}}"></script>
    <script src="{{asset('libs/daterangepicker/js/daterangepicker.js')}}"></script>
    <script>
        function switch_check(){
            if ($('select[name="type"]').val() === 'banque') {
                $('.banque-fields').show();
                $('#banque, #adresse, #rib').prop('required', true);
            } else {
                $('.banque-fields').hide();
                $('#banque, #adresse, #rib').prop('required', false);
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            $('select[name="type"]').on('change', function() {
                switch_check()
            });
        });
        switch_check()
        $('#rib').inputmask()
    </script>
    <script>
        $('#type-select').select2({
            width: '100%',
            placeholder:'Selectioner un type',
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
