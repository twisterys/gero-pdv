<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{env('APP_NAME')}} | Se connecter </title>
    @include('layouts.head')
    <style>
        .bg {
            background: linear-gradient(to top, rgba(70, 84, 97, 0.65), rgba(59, 84, 97, 1)), url({{asset('images/login-bg.jpg')}}) no-repeat top center;
            background-size: cover;
            filter: blur(2px);
            -webkit-filter: blur(2px);
            top: 0;
            left: 0;
            z-index: -1;
        }
    </style>
</head>
<body>

<div class="w-100 row bg-primary vh-100 position-relative m-0">
    <div class="col-lg-8 position-relative d-none d-lg-block d-flex pt-5 justify-content-center z-2">
        <div class="position-absolute bg w-100 h-100"></div>
    </div>
    <div class="col-lg-4 bg-white rounded-start-4 h-100 d-flex align-items-center justify-content-center z-3">
        <div class="card-body p-0 mb-5" style="max-width: 550px">
            <h3 class="text-center m-0">
                <a class="logo logo-admin"><img src="{{asset('images/logo-dark.png')}}" height="100" alt="logo" class="my-3"></a>
            </h3>
            <div class="px-2 mt-2">
                <h4 class="font-size-18 mb-2 text-center">Bienvenue chez Gero</h4>
                <p class="text-muted text-center">Votre Partenaire pour une Gestion Commerciale Efficace.</p>
                <form action="{{route('auth.authentifier')}}" method="post" class="form-horizontal needs-validation my-4" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required" for="email">Email</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="far fa-user"></i></span>
                            <input type="text" required value="{{old('i_email')}}" class="form-control @if($errors->has('i_email')) is-invalid @endif" name="i_email" id="email">
                            @if($errors->has('i_email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('i_email') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required" for="password">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon2"><i class="fa fa-key"></i></span>
                            <input required type="password" class="form-control" id="password" name="i_password">
                        </div>
                    </div>
                    <div class="mb-3 row mt-4">
                        <div class="col-sm-6">
                            <div class="form-check">
                                <input type="checkbox" name="i_souviens" class="form-check-input" id="customControlInline">
                                <label class="form-check-label" for="customControlInline">Souviens moi?</label>
                            </div>
                        </div>
                        <div class="col-sm-6 text-end">
                            <a href="{{route('password.request')}}" class="text-muted">Mot de passe oublié?</a>
                        </div>
                    </div>
                    <div class="mb-3 mb-0 row">
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Se connecter <i class="fas fa-sign-in-alt ms-1"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.foot')
<script src="{{asset('js/form-validation.init.js')}}"></script>
</body>
</html>
