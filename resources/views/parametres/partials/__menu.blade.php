<li><a  class="list-group-item  @if(Request::url() === route('informations.modifier')) active @endif"  href="{{route('informations.modifier')}}"> Informations Entreprise </a></li>
<li><a  class="list-group-item  @if(Request::url() === route('references.liste')) active @endif"  href="{{route('references.liste')}}"> Références </a></li>
<li><a  class="list-group-item  @if(Request::url() === route('produits-settings.modifier')) active @endif"  href="{{route('produits-settings.modifier')}}"> Produits </a></li>
<li><a class="list-group-item @if(Request::url() === route('documents.modifier')) active @endif"  href="{{route('documents.modifier')}}"> Mise en page PDF</a></li>
<li><a class="list-group-item @if(Request::url() === route('modules.modifier')) active @endif"  href="{{route('modules.modifier')}}">Gestion des documents</a></li>
<li><a  class="list-group-item @if(Request::url() === route('methodes_paiement.liste')) active @endif"  href="{{route('methodes_paiement.liste')}}">Méthodes de paiement </a></li>
@if(\App\Services\LimiteService::is_enabled('methode_livraison'))
<li><a  class="list-group-item @if(Request::url() === route('methodes-livraison.liste')) active @endif"  href="{{route('methodes-livraison.liste')}}">Méthodes de livraison </a></li>
@endif
@if(\App\Services\LimiteService::is_enabled('magasin_extra'))
    <li><a class="list-group-item @if(Request::url() === route('magasin.liste')) active @endif" href="{{route('magasin.liste')}}">Magasins </a></li>
@endif
<li><a  class="list-group-item  @if(Request::url() === route('unites.liste')) active @endif" href="{{route('unites.liste')}}"> Unités </a></li>
<li><a  class="list-group-item @if(Request::url() === route('taxes.liste')) active @endif  "href="{{route('taxes.liste')}}"> Taxes </a></li>
<li><a  class="list-group-item @if(Request::url() === route('balises.liste')) active @endif " href="{{route('balises.liste')}}"> Étiquettes </a></li>
<li><a  class="list-group-item @if(Request::url() === route('categories.liste')) active @endif " href="{{route('categories.liste')}}"> Catégories de dépense </a></li>
<li><a  class="list-group-item @if(Request::url() === route('formes_juridique.liste')) active @endif " href="{{route('formes_juridique.liste')}}"> Formes juridique </a></li>
<li><a  class="list-group-item @if(Request::url() === route('operations.liste')) active @endif " href="{{route('operations.liste')}}"> Opérations bancaires </a></li>
<li><a  class="list-group-item @if(Request::url() === route('fonctionnalites.modifier')) active @endif " href="{{route('fonctionnalites.modifier')}}"> Fonctionnalités </a></li>
<li><a  class="list-group-item @if(Request::url() === route('compteurs.modifier')) active @endif " href="{{route('compteurs.modifier')}}"> Compteurs </a></li>
@if(\App\Services\LimiteService::is_enabled('pos'))
    <li><a  class="list-group-item @if(Request::url() === route('pos-settings.modifier')) active @endif " href="{{route('pos-settings.modifier')}}"> Point de vente</a></li>
@endif
