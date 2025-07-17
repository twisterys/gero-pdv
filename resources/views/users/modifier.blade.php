@extends('layouts.main')
@section('document-title','Modifier un utilisateur')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('utilisateurs.mettre_jour',$o_utilisateur->id)}}" method="post">
                        @csrf
                        @method('PUT')
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                                <div>
                                    <a href="{{route('utilisateurs.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i
                                        <i class="mdi me-2 text-success mdi-account-group"></i>
                                        Modifier un utilisateur
                                    </h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>

                            </div>
                            <hr class="border">
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12 mt-2">
                                <label for="name-input" class="form-label required">Nom complet</label>
                                <input id="name-input" type="text"
                                       class="form-control @error('i_nom')  is-invalid @enderror " required
                                       name="i_nom" value="{{old('i_nom',$o_utilisateur->name)}}">
                                @error('i_nom')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 col-12 mt-2">
                                <label for="email-input" class="form-label required">Email</label>
                                <input type="email" id="email-input"
                                       class="form-control @error('i_email') is-invalid @enderror " required
                                       name="i_email" value="{{old('i_email',$o_utilisateur->email)}}">
                                @error('i_email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 col-12 mt-2">
                                <label for="" class="form-label">Mot de passe</label>
                                <div class="input-group">
                                    <input type="password" id="password-input"
                                           class="form-control @error('i_password') is-invalid @enderror "
                                           name="i_password" value="{{old('i_password')}}">
                                    <button class="btn btn-light show-pass" type="button"><i class="fa fa-eye"></i>
                                    </button>
                                    @error('i_password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-12 mt-2 ">
                                <label for="roles" class="form-label"> Role</label>
                                <select name="i_role" id="roles" class="form-select  @error('i_role') is-invalid @enderror">
                                    @foreach($roles as $role)
                                        <option @selected(old('i_role',$o_utilisateur->hasRole($role->name)) == $role->name) value="{{$role->name}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                                @error('i_role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-4 col-12 mt-2">
                                <label for="dashboard" class="form-label required">Tableau de bord</label>
                                <select name="i_dashboard" id="dashboard" class="form-select @error('i_dashboard') is-invalid @enderror">
                                    @foreach($dashboards as $dashboard)
                                        <option
                                            @selected(
                                                in_array($dashboard->id, (array) old('i_dashboard', [])) ||
                                                in_array($dashboard->id, $o_utilisateur->dashboards->pluck('id')->toArray())
                                            )
                                            value="{{ $dashboard->id }}">
                                            {{ $dashboard->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('i_dashboard')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-4 col-12 mt-2">
                                <label for="magasins" class="form-label">Magasins</label>
                                <select name="i_magasins[]" id="magasins" class="form-select @error('i_magasins') is-invalid @enderror" multiple>
                                    @foreach($magasins as $magasin)
                                        <option @selected(in_array($magasin->id,old('i_magasins',[])) || in_array($magasin->id,$o_utilisateur->magasins->pluck('id')->toArray()) ) value="{{$magasin->id}}">{{$magasin->reference}}</option>
                                    @endforeach
                                </select>
                                @error('i_magasins')
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
    </div>
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{asset('js/form-validation.init.js')}}"></script>
    <script>
        const client_select_ajax_link = '{{ route('clients.select') }}';
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('utilisateurs.liste')}}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {}
        const __dataTable_filter_trigger_button_id = '#search-btn';
        $('.show-pass').click(function () {
            if ($('#password-input').attr('type') === 'password') {
                $('#password-input').attr('type', 'text')
                $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash')
            } else {
                $('#password-input').attr('type', 'password')
                $(this).find('i').addClass('fa-eye').removeClass('fa-eye-slash')
            }
        })
        $('#magasins').select2({
            minimumResultsForSearch: -1,
            multiple:!0
        })
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
@endpush
