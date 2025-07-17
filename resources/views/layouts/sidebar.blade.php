@php use App\Models\Achat;use App\Models\Vente;use App\Services\LimiteService;use App\Services\ModuleService;use Illuminate\Support\Facades\Request; @endphp
@php
    $modules = ModuleService::getActiveModules();
@endphp
    <!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu"
     @if(\Carbon\Carbon::make(session('tenant_expiration_date'))->diffInDays(now()) <= 30) style="padding-top: 50px !important" @endif>
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <!-- Dashboard -->
                <li>
                    <a href="{{url('/')}}" class=" waves-effect">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                @if(auth()->user()->canAny(['client.liste','fournisseur.liste','commercial.liste']))
                    <li @if(in_array(Request::segment(1),['clients','commercials','fournisseurs'])) class="mm-active" @endif>
                        <a href="javascript: void(0);"
                           class="has-arrow  waves-effect @if(in_array(Request::segment(1),['clients','commercials','fournisseurs'])) mm-active @endif"
                           aria-expanded="false">
                            <i class="mdi mdi-contacts"></i>
                            <span>Contacts</span>
                        </a>
                        <ul class="sub-menu mm-collapse @if(in_array(Request::segment(1),['clients','commercials','fournisseurs'])) mm-show @endif"
                            aria-expanded="false">
                            @can('client.liste')

                                <li @if(Request::segment(1)==='clients') class="mm-active" @endif><a
                                        href="{{ route('clients.liste') }}"
                                        @if(Request::segment(1)==='clients') class="active" @endif >Client</a></li>
                            @endcan
                            @can('fournisseur.liste')
                                <li @if(Request::segment(1)==='fournisseurs') class="mm-active" @endif><a
                                        href="{{ route('fournisseurs.liste') }}"
                                        @if(Request::segment(1)==='fournisseurs') class="active" @endif>Fournisseur</a>
                                </li>
                            @endcan
                            @if(LimiteService::is_enabled('commerciaux'))
                                @can('commercial.liste')
                                    <li @if(Request::segment(1)==='commercials') class="mm-active" @endif><a
                                            href="{{ route('commercials.liste') }}"
                                            @if(Request::segment(1)==='commercials') class="active" @endif >Commerciaux</a>
                                    </li>
                                @endcan
                            @endif
                        </ul>
                    </li>
                @endif
                <li @if(in_array(Request::segment(1),['articles','familles'])) class="mm-active" @endif >
                    <a href="javascript: void(0);"
                       class="has-arrow waves-effect @if(in_array(Request::segment(1),['articles','familles'])) mm-active @endif"
                       aria-expanded="false">
                        <i class="fa  fas fa-barcode"></i>
                        <span>Produits</span>
                    </a>
                    <ul class="sub-menu mm-collapse @if(in_array(Request::segment(1),['articles','familles'])) mm-show @endif "
                        aria-expanded="false">
                        <li @if(Request::segment(1)==='articles') class="mm-active" @endif><a
                                href="{{ route('articles.liste') }}"
                                @if(Request::segment(1)==='articles') class="active" @endif >Articles</a></li>
                        <li @if(Request::segment(1)==='familles') class="mm-active" @endif><a
                                href="{{ route('familles.liste') }}"
                                @if(Request::segment(1)==='familles') class="active" @endif >Familles</a></li>
                        <li @if(Request::segment(1)==='marques') class="mm-active" @endif><a
                                href="{{ route('marques.liste') }}"
                                @if(Request::segment(1)==='marques') class="active" @endif >Marques</a></li>
                        {{--                        <li><a href="#br">Importation</a></li>--}}
                    </ul>
                </li>
                @if(LimiteService::is_enabled('pos') && auth()->user()->can('pos.*'))
                    <li>
                        <a href="{{route('pos')}}" class=" waves-effect">
                            <i class="mdi mdi-cash-register"></i>
                            <span>Point de vente</span>
                        </a>
                    </li>
                @endif
                @can('vente.liste')
                    <li @if(in_array(Request::segment(1),['ventes'])) class="mm-active" @endif >
                        <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                            <i class="mdi mdi-chart-bell-curve-cumulative"></i>
                            <span>Ventes</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            @foreach(Vente::TYPES as $type)
                                @if(in_array($type,$modules))
                                    <li @if(Request::segment(2)===$type && Request::segment(1)==='ventes') class="mm-active" @endif >
                                        <a href="{{ route('ventes.liste', ['type' => $type]) }}"
                                           @if(Request::segment(2)===$type && Request::segment(1)==='ventes') class="active" @endif>@lang('ventes.'.$type.'.sidebar')</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endcan

                @can('achat.liste')
                    <li @if(in_array(Request::segment(1),['achats'])) class="mm-active" @endif>
                        <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                            <i class="mdi mdi-shopping"></i>
                            <span>Achats</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            @foreach(Achat::TYPES as $type)
                                @if(in_array($type,$modules))
                                    <li @if(Request::segment(2)=== $type && Request::segment(1)==='achats') class="mm-active" @endif >
                                        <a href="{{ route('achats.liste', ['type' => $type]) }}"
                                           @if(Request::segment(2)===$type && Request::segment(1)==='achats') class="active" @endif>@lang('achats.'.$type.'.sidebar')</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endcan
               @if(LimiteService::is_enabled('transformation'))
                   @can('transformation.liste')
                        <li @if(in_array(Request::segment(1),['transformations'])) class="mm-active" @endif>
                            <a href="{{route('transformations.liste')}}" aria-expanded="false">
                                <i class="mdi mdi-switch"></i>
                                <span>Transformations</span>
                            </a>
                        </li>
                   @endcan
               @endif
                @can('depense.liste')
                    <li @if(in_array(Request::segment(1),['depenses'])) class="mm-active" @endif>
                        <a href="{{route('depenses.liste')}}" aria-expanded="false">
                            <i class="mdi mdi-wallet"></i>
                            <span>Dépenses</span>
                        </a>
                    </li>
                @endcan

                @can('cheque.liste')
                    <li @if(in_array(Request::segment(1),['cheques'])) class="mm-active" @endif>
                        <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                            <i class="mdi mdi-cash-100"></i>
                            <span>Chéques</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            <li @if(Request::segment(2)=== 'encaisser' && Request::segment(1)==='cheques') class="mm-active" @endif >
                                <a href="{{route('cheques.encaisser_liste')}}"
                                   @if(Request::segment(2)==='encaisser'&& Request::segment(1)==='cheques') class="active" @endif>Chéques
                                    à encaisser</a>
                            </li>
                            <li @if(Request::segment(2)=== 'decaisser' && Request::segment(1)==='cheques') class="mm-active" @endif >
                                <a href="{{route('cheques.decaisser_liste')}}"
                                   @if(Request::segment(2)==='decaisser'&& Request::segment(1)==='cheques') class="active" @endif>Chéques
                                    à décaisser</a>
                            </li>
                        </ul>
                    </li>
                @endcan


                @if(LimiteService::is_enabled('abonnement'))
                    <li @if(in_array(Request::segment(1),['abonnements'])) class="mm-active" @endif>
                        <a href="{{route('abonnements.liste')}}" aria-expanded="false">
                            <i class="mdi mdi-repeat"></i>
                            <span>Abonnements</span>
                        </a>
                    </li>
                @endif

                @if(LimiteService::is_enabled('stock') && auth()->user()->canAny(['inventaire.*','transfert_stock.*']))
                    <li @if(in_array(Request::segment(1),['stock'])) class="mm-active" @endif>
                        <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                            <i class="fa fas fa-boxes"></i>
                            <span>Stock</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            @can('transfert_stock.*')
                                <li @if(Request::segment(2)==='transfert' && Request::segment(1)==='stock') class="mm-active" @endif >
                                    <a @if(Request::segment(2)==='transfert' && Request::segment(1)==='stock') class="active"
                                       @endif href="{{ route('transferts.liste') }}">Transfert de stock</a>
                                </li>
                            @endcan
                            @can('inventaire.*')
                                <li @if(Request::segment(2)==='inventaire' && Request::segment(1)==='stock') class="mm-active" @endif >
                                    <a @if(Request::segment(2)==='inventaire' && Request::segment(1)==='stock') class="active"
                                       @endif href="{{ route('inventaire-liste') }}">Inventaire</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif


                @canAny(['compte.liste','paiement.liste'])
                    <li @if(in_array(Request::segment(1),['tresorerie'])) class="mm-active" @endif>
                        <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                            <i class="fa  fas fa-university"></i>
                            <span>Trésorerie</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            {{--                        <li><a href="#DP">Paiments</a></li>--}}
                            <li @if(Request::segment(2)==='comptes' && Request::segment(1)==='tresorerie') class="mm-active" @endif >
                                <a @if(Request::segment(2)==='comptes' && Request::segment(1)==='tresorerie') class="active"
                                   @endif href="{{ route('comptes.liste') }}">Comptes</a>
                            </li>
                            <li @if(Request::segment(2)==='paiement' && Request::segment(1)==='tresorerie') class="mm-active" @endif >
                                <a @if(Request::segment(2)==='paiement' && Request::segment(1)==='tresorerie') class="active"
                                   @endif href="{{ route('paiement.liste') }}">Paiements</a>
                            </li>
                        </ul>
                    </li>
                @endcanAny
                @can('rapport.*')
                    <li @if(in_array(Request::segment(1),['rapports'])) class="mm-active" @endif >
                        <a href="{{route('rapports.liste')}}" class=" waves-effect">
                            <i class="fa fa-chart-pie"></i>
                            <span>Rapports</span>
                        </a>

                    </li>
                @endcan
                @canAny(['importer.*','exporter.*'])
                    <li @if(in_array(Request::segment(1),['importation','exportation'])) class="mm-active" @endif>
                        <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                            <i class="fa fa-database"></i>
                            <span>Import/Export</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            <li @if(Request::segment(1)==='importation') class="mm-active" @endif >
                                <a @if(Request::segment(1)==='importation' ) class="active"
                                   @endif href="{{ route('importer-liste') }}">Importation</a>
                            </li>
                            <li @if(Request::segment(1)==='exportation') class="mm-active" @endif >
                                <a @if( Request::segment(1)==='exportation') class="active"
                                   @endif href="{{route('exporter-liste')}}">Exportation</a>
                            </li>
                        </ul>
                    </li>
                @endcanany
                @canany(['utilisateur.liste','permission.liste'])
                    <li class="">
                        <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                            <i class="mdi mdi-account-group"></i>
                            <span>Utilisateurs</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            @if(LimiteService::is_enabled('users'))
                                @can('utilisateur.liste')
                                    <li @if(Request::segment(1)==='utilisateurs') class="mm-active" @endif>
                                        <a href="{{route('utilisateurs.liste')}}"
                                           class=" waves-effect @if(Request::segment(1)==='utilisateurs') active @endif">
                                            <span>Utilisateurs</span>
                                        </a>
                                    </li>
                                @endcan
                            @endif
                            @can('permission.liste')
                                <li @if(Request::segment(1)==='permissions') class="mm-active" @endif >
                                    <a href="{{route('permissions.liste')}}"
                                       class="waves-effect @if(Request::segment(1)==='permissions') active @endif">
                                        <span>Roles</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>

                @endcanany
                @if(LimiteService::is_enabled('affaires') && auth()->user()->can('affaire.liste'))
                    <li @if(Request::segment(1)==='affaire') class="mm-active" @endif >
                        <a href="{{route('affaire.liste')}}"
                           class="waves-effect @if(Request::segment(1)==='affaire') active @endif">
                            <i class=" fas fa-file-invoice-dollar"></i>
                            <span>Affaires</span>
                        </a>
                    </li>
                @endif
                @if(LimiteService::is_enabled('activites') && auth()->user()->can('activite.liste'))
                    <li @if(Request::segment(1)==='events') class="mm-active" @endif >
                        <a href="{{route('events.liste')}}"
                           class="waves-effect @if(Request::segment(1)==='events') active @endif">
                            <i class=" fas fa-bell"></i>
                            <span>Activités</span>
                        </a>
                    </li>
                @endif
                @can('parametres.*')
                    <li @if(Request::segment(1)==='parametres' ) class="mm-active" @endif >
                        <a href="{{ url('parametres') }}" class="waves-effect">
                            <i class="mdi mdi-cog"></i>
                            <span>Paramètres</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
