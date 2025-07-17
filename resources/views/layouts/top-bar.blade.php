<header id="page-topbar">
    @if(\Carbon\Carbon::make(session('tenant_expiration_date'))->diffInDays(now()) <= 30)
        <div class="py-2 bg-danger">
            <h5 class="text-white mb-0 text-center  " >
               <i class="fa fa-exclamation-triangle "></i> Votre licence expire bientôt! Renouvelez-la dans les {{\Carbon\Carbon::make(session('tenant_expiration_date'))->diffInDays(now())}} prochains jours pour éviter toute interruption de service  <i class="fa fa-exclamation-triangle "></i>
            </h5>
        </div>
    @endif

    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{route('tableau_bord.liste')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('images/logo-sm.png')}}" alt="" height="35">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('images/logo-light.png')}}" alt="" height="45">
                    </span>
                </a>
                <a href="{{route('tableau_bord.liste')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{asset('images/logo-sm.png')}}" alt="" height="35">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('images/logo-light.png')}}" alt="" height="45">
                    </span>
                </a>
            </div>
            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="mdi mdi-menu"></i>
            </button>
        </div>
            <p class=" d-none d-md-inline text-white-50 text-center m-0 h5" > {{session()->get('nom_entreprise')}}</p>
        <div class="d-flex">
            <!-- Notification -->
{{--            <div class="dropdown d-inline-block">--}}
{{--                <button type="button" class="btn header-item noti-icon waves-effect notification-step"--}}
{{--                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"--}}
{{--                        aria-expanded="false">--}}
{{--                    <i class="mdi mdi-bell-outline"></i>--}}
{{--                    <span class="badge bg-danger rounded-pill">2</span>--}}
{{--                </button>--}}
{{--                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"--}}
{{--                     aria-labelledby="page-header-notifications-dropdown">--}}
{{--                    <div class="p-3">--}}
{{--                        <h6 class="m-0">Notifications (258) </h6>--}}
{{--                    </div>--}}
{{--                    <div data-simplebar style="max-height: 230px;">--}}
{{--                        <a href="" class="text-reset notification-item">--}}
{{--                            <div class="d-flex align-items-start">--}}
{{--                                <div class="avatar-xs me-3">--}}
{{--                                    <span class="avatar-title bg-primary rounded-circle font-size-16">--}}
{{--                                        <i class="mdi mdi-cart-outline"></i>--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <div class="flex-1">--}}
{{--                                    <h6 class="mb-1 font-size-15">Your order is placed</h6>--}}
{{--                                    <div class="text-muted">--}}
{{--                                        <p class="mb-1 font-size-12">Dummy text of the printing and typesetting--}}
{{--                                            industry.</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                    <div class="p-2 border-top d-grid">--}}
{{--                        <a class="btn btn-sm btn-link font-size-14 btn-block text-center" href="javascript:void(0)">--}}
{{--                            <i class="mdi mdi-arrow-right-circle me-1"></i> View all--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- full-screen -->
            <div class="dropdown  d-flex align-items-center ms-1">
                <button id="exercice-btn" class="btn text-white-50 h5 m-0 pt-2" >{{session()->get('exercice')}}</button>
                <button type="button" class="d-none d-md-inline btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="mdi mdi-fullscreen"></i>
                </button>
            </div>


            <!-- User -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect user-step" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-none d-xl-inline-block ms-1">{{auth()->user()->name}}</span>
                    <span class="d-xl-none d-inline-block ms-1"><i class="fa fa-user"></i></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a href="{{route('utilisateurs.ma_licence')}}" class="dropdown-item"><i class="mdi mdi-license text-success me-2"></i>Ma licence</a>
                    <a href="{{route('auth.modifier')}}" class="dropdown-item"><i class="mdi mdi-security text-warning me-2"></i>Sécurité</a>
                    <div class="dropdown-divider"></div>

                    <form method="post" action="{{route('auth.se-deconnecter')}}">
                        @csrf
                        <button class="dropdown-item"><i class="dripicons-exit d-inline-block text-danger me-2"></i>Se déconnecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
