<!doctype html>
<html  lang="fr">
<head>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{env('APP_NAME')}} | @yield('document-title') </title>
    @stack('styles')
    @include('layouts.head')
    <link rel="icon" href="{{asset('images/logo-sm.png')}}">
    <link rel="stylesheet" href="{{asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
{{--    <style>--}}
{{--        #exercice-btn {--}}
{{--            transition: all .3s ease-in-out;--}}
{{--            box-shadow: 0 0 10px rgba(0,0,0,.2);--}}
{{--            position: fixed;--}}
{{--        }--}}
{{--        #exercice-btn:hover{--}}
{{--            scale: 1.1;--}}
{{--        }--}}
{{--        #exercice-btn::before {--}}
{{--            transition: all .3s ease-in-out;--}}
{{--            content: " ";--}}
{{--            position: absolute;--}}
{{--            width: 120%;--}}
{{--            height: 120%;--}}
{{--            background-color: var(--bs-success);--}}
{{--            opacity: 0;--}}
{{--            top: -1rem;--}}
{{--            left: -.5rem;--}}
{{--            border-radius: 50%;--}}
{{--            z-index: -1;--}}
{{--        }--}}
{{--        #exercice-btn:hover::before {--}}
{{--            opacity: .5;--}}

{{--        }--}}
{{--    </style>--}}
</head>
<body data-topbar="dark" style="overflow: hidden; height: 100vh">
<div class="loader-container">
    <div class="loader"></div>
</div>
{{--<div id="exercice-btn" class="bg-success text-white  ps-5 pe-4 pb-5 pt-4 rounded-circle h6 m-0" role="button" style="z-index: 9999; left: -2rem;bottom: -2rem">--}}
{{--    <div style="transform: translateX(-.2rem) translateY(.2rem)"  >2024</div>--}}
{{--</div>--}}
@include('layouts.top-bar')
@if(!(auth()->user()->hasRole('vendeur')))
@include('layouts.sidebar')
@endif
<div class="main-content @if(auth()->user()->hasRole('vendeur')) ms-0 @endif">
    <div class="page-content" @if(\Carbon\Carbon::make(session('tenant_expiration_date'))->diffInDays(now()) <= 30) style="padding-top: 140px !important"  @endif>
        <div class="container-fluid px-1 px-md-3">
            @yield('page')
        </div>
    </div>
    <!-- End Page-content -->
    <footer class="footer @if(auth()->user()->hasRole('vendeur')) start-0 @endif">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script>document.write(new Date().getFullYear())</script>
                    © Gero.
                </div>
                <div class="col-sm-6">
                    <div class="text-sm-end d-none d-sm-block">
                        Crafted by Tarmiz
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
<div class="modal fade" id="exercise-modal" tabindex="-1" aria-labelledby="exercise-modal-title" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


</body>
@include('layouts.foot')
<script>
</script>
@if(session()->has('success'))
    <script>
        $(document).ready(function (){
            toastr.success("{!!session()->get('success')!!}")
        })
    </script>
@endif
@if(session()->has('error'))
    <script>
        $(document).ready(function (){

            toastr.error("{!!session()->get('error')!!}")
        })

    </script>
@endif
@if(session()->has('warning'))
    <script>
        $(document).ready(function (){

            toastr.warning("{!!session()->get('warning')!!}")
        })

    </script>
@endif
@if(session()->has('info'))
    <script>
        $(document).ready(function (){
            toastr.info("{!!session()->get('info')!!}")
        })
    </script>
@endif
@stack('scripts')
<script>
    const __csrf_token = '{{csrf_token()}}'
    const __exercice_change_url ="{{route('exercice.changer')}}";

</script>
</html>
