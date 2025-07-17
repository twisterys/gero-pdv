@extends('layouts.main')
@section('document-title','Modifier mot de passe')
@push('styles')
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div>
                                <a href="#"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3"><i class="mdi mdi-lock me-2 text-success"></i>
                                    Sécurité
                                </h5>
                            </div>

                        </div>
                        <hr class="border">
                    </div>
                    <div class="row">
                        <div class="col-sm-6 row mx-0 col-12">
                            <form action="{{route('auth.sauvegarder')}}" method="POST">
                                @csrf
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Changement de mot de passe</h5>
                                    <hr class="border border-success">
                                </div>

                                <div class="row">
                                    <div class=" col-8 mt-2 ">
                                        <label for="name-input" class="form-label required">Ancien mot de passe</label>
                                        <input id="name-input" type="password" class="form-control @error('i_ancien_mot_de_passe')  is-invalid @enderror " required
                                               name="i_ancien_mot_de_passe" value="{{old('i_ancien_mot_de_passe')}}">
                                        @error('i_ancien_mot_de_passe')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-8 mt-2 ">
                                        <label for="new-password" class="form-label required">Nouveau mot de passe</label>
                                        <input id="new-password" type="password" class="form-control @error('i_nouveau_mot_de_passe')  is-invalid @enderror " required
                                               name="i_nouveau_mot_de_passe" value="{{old('i_nouveau_mot_de_passe')}}">

                                        <div class="invalid-feedback" id="new-password-error">
                                            @error('i_nouveau_mot_de_passe')
                                            {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 mt-2">
                                        <label for="confirm-password" class="form-label required">Confirmation de mot de passe</label>
                                        <input id="confirm-password" type="password" class="form-control @error('i_confirmer_mot_de_passe')  is-invalid @enderror " required
                                               name="i_confirmer_mot_de_passe" value="{{old('i_confirmer_mot_de_passe')}}">

                                        <div class="invalid-feedback" id="confirm-password-error">
                                            @error('i_confirmer_mot_de_passe')
                                            {{ $message }}
                                            @enderror
                                        </div>
                                        <div id="confirm-password-success" class="valid-feedback"></div>
                                    </div>
                                </div>


                                <div class="col-12 mt-4 p-2 d-flex justify-content-start">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>

                            </form>
                        </div>
                        <div class="col-sm-6 row mx-0 col-12">
                            <form action="{{route('auth.sauvegarder_email')}}" method="POST">
                                @csrf
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Changement d'email</h5>
                                    <hr class="border border-success">
                                </div>

                                <div class="row">
                                    <div class=" col-8 mt-2 ">
                                        <label for="email-input" class="form-label required">Email</label>
                                        <input id="email-input" type="text" class="form-control @error('email')  is-invalid @enderror " required
                                               name="email" value="{{old('email',$email)}}">
                                        @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-8 mt-2 ">
                                        <label for="password" class="form-label required">Mot de passe</label>
                                        <input id="password" type="password" class="form-control @error('mot_de_passe')  is-invalid @enderror " required
                                               name="mot_de_passe" value="{{old('mot_de_passe')}}">

                                        <div class="invalid-feedback" >
                                            @error('mot_de_passe')
                                            {{ $message }}
                                                test
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4 p-2 d-flex justify-content-start">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0 float-end ms-3"><i class="mdi mdi-lock me-2 text-success"></i>
                                    Dernières connexions
                                </h5>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">Utilisateur</th>
                            <th scope="col">Date de connexion</th>
                            <th scope="col">Date de déconnexion</th>
                            <th scope="col">Agent</th>
                            <th scope="col">Adresse IP</th>
                            <th scope="col">Location</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($results  as $result)
                            <tr>
                                <td>{{ $result->user_name }}</td>
                                <td>{{ $result->login_at }}</td>
                                <td>{{ $result->logout_at }}</td>
                                <td>{{ $result->user_agent }}</td>
                                <td>{{ $result->ip_address }}</td>
                                <td>{{ $result->location }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const newPasswordInput = document.getElementById('new-password');
            const confirmPasswordInput = document.getElementById('confirm-password');
            const newErrorElement = document.getElementById('new-password-error');
            const confirmErrorElement = document.getElementById('confirm-password-error');
            const confirmSuccessElement = document.getElementById('confirm-password-success');

            // Vérification du nouveau mot de passe
            newPasswordInput.addEventListener('input', function () {
                const password = newPasswordInput.value;

                if (password.length < 8) {
                    newPasswordInput.classList.add('is-invalid');
                    newErrorElement.textContent = 'Le mot de passe doit contenir au moins 8 caractères.';
                } else {
                    newPasswordInput.classList.remove('is-invalid');
                    newErrorElement.textContent = '';
                }

                // Vérification de la confirmation du mot de passe si une valeur existe
                if (confirmPasswordInput.value && password !== confirmPasswordInput.value) {
                    confirmPasswordInput.classList.add('is-invalid');
                    confirmErrorElement.textContent = 'La confirmation du mot de passe ne correspond pas.';
                    confirmSuccessElement.textContent = '';
                } else if (confirmPasswordInput.value && password === confirmPasswordInput.value) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    confirmErrorElement.textContent = '';
                    confirmSuccessElement.textContent = 'Mot de passe confirmé avec succès.';
                }
            });

            // Vérification de la confirmation du mot de passe
            confirmPasswordInput.addEventListener('input', function () {
                const password = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (password !== confirmPassword) {
                    confirmPasswordInput.classList.add('is-invalid');
                    confirmErrorElement.textContent = 'La confirmation du mot de passe ne correspond pas.';
                    confirmSuccessElement.textContent = '';
                } else {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    confirmErrorElement.textContent = '';
                    confirmSuccessElement.textContent = 'Mot de passe confirmé avec succès.';
                }
            });
        });
    </script>
@endpush
