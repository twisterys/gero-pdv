@extends('layouts.main')
@section('document-title','Tableau de bord')
@push('styles')
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{ route('tableau_bord.mettre_a_jour') }}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Personalisation de tableau de bord</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    @method('PUT')
                   <div class="row mx-0">
                       <div class="col-md-4 col-sm-6">
                           <label for="type-input" class="form-label">Type Global</label>
                           <select name="type" id="type-input" class="form-select @error('type') is-invalid @enderror">
                               @foreach($tableaux_de_bord as $tableau_de_bord)
                                   <option @selected(old('type', $current) == $tableau_de_bord->function_name) value="{{$tableau_de_bord->function_name}}">{{$tableau_de_bord->name}}</option>
                               @endforeach
                           </select>
                           @error('type')
                           <div class="invalid-feedback">
                               {{ $message }}
                           </div>
                           @enderror
                       </div>
                       <div class="col-md-4 col-sm-6">
                           <label for="date-input" class="form-label">Date par défaut </label>
                           <select name="date" id="date-input" class="form-select @error('date') is-invalid @enderror  ">
                               <option @selected(old('date', $current_date) == 'year' ) value="year">Année d'exercice</option>
                               <option  @selected(old('date', $current_date) == 'today') value="today">Aujourd'hui</option>
                               <option @selected(old('date', $current_date) == 'week') value="week">Semaine en cours</option>
                               <option @selected(old('month', $current_date) == 'month') value="month">Mois en cours</option>
                           </select>
                           @error('date')
                           <div class="invalid-feedback">
                               {{ $message }}
                           </div>
                           @enderror
                       </div>
                   </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
@endpush

