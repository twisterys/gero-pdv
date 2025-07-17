<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }} | Réinitialiser le mot de passe </title>
    @include('layouts.head')
    <link rel="icon" href="{{ asset('images/logo-sm.png') }}">
    <link rel="stylesheet" href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
    @stack('styles')
    <style>
        .main-content {
            margin-left: 0% !important;
        }
        .footer{
            left: 0px;
        }

    </style>
</head>
<body>
<div class="main-content">
    <div class="page-content" >
        <div class="container-fluid ">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">{{ __('Réinitialiser le mot de passe') }}</div>

                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf

                                    <div class="row mb-3">
                                        <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Adresse email') }}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Envoyer le lien de réinitialisation du mot de passe') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin du contenu de la page -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script>document.write(new Date().getFullYear())</script> © Gero.
                </div>
                <div class="col-sm-6">
                    <div class="text-sm-end d-none d-sm-block">
                        Conçu par Tarmiz
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

@include('layouts.foot')

@if(session()->has('success'))
    <script>
        $(document).ready(function (){
            toastr.success("{!! session()->get('success') !!}")
        })
    </script>
@endif
@if(session()->has('error'))
    <script>
        $(document).ready(function (){
            toastr.error("{!! session()->get('error') !!}")
        })
    </script>
@endif
@if(session()->has('warning'))
    <script>
        $(document).ready(function (){
            toastr.warning("{!! session()->get('warning') !!}")
        })
    </script>
@endif
@if(session()->has('info'))
    <script>
        $(document).ready(function (){
            toastr.info("{!! session()->get('info') !!}")
        })
    </script>
@endif
@stack('scripts')
<script>
    const __csrf_token = '{{ csrf_token() }}';
</script>
</body>
</html>
