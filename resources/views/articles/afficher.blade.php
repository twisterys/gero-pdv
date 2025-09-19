@extends('layouts.main')
@section('document-title', 'Articles')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
@endpush
@section('page')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div class="d-flex switch-filter justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('articles.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts me-2 text-success"></i>
                                    Voir l'article : {{$o_article->designation}}
                                </h5>
                            </div>
                            <a class="btn btn-soft-warning" href="{{route('articles.modifier',$o_article->id)}}"><i
                                    class="fa fa-edit"></i> <span class="d-none d-sm-block">Modifier</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-4  col-md-6 col-12">
            <div class="card overflow-hidden">
                <div class="rounded overflow-hidden "
                     style="max-width: 100%">
                    @if($o_article->image)
                        <img src="{{route('article.image.load', $o_article->image)}}"
                             class="border-0 w-100" alt="">
                    @else
                        <img src="https://placehold.co/150x150?text={{$o_article->reference}}"
                             class="border-0 w-100" alt="">
                    @endif
                </div>
                <div class="card-body overflow-hidden p-0">
                    <div class="row mx-0">
                        <div
                            class="col-12 p-5 py-3 text-center d-flex flex-column align-items-center ">
                            <h5 class="text-center text-primary-50 mt-3 mb-0">{{$o_article->designation}}
                                <br>{{$o_article->reference}}
                                @if($o_article->marque)
                                <br>
                                Marque: {{$o_article->marque->nom}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-9 col-xl-8 col-md-6 col-12 d-flex">
            <div class="card w-100">
                <div class="card-body">
                    <div class="card-title">
                        <h5>
                           Information d'article
                        </h5>
                        <hr class="border">
                    </div>
                    <div class="row">
                        <div class=" col-xl-3 col-lg-6  my-1   d-flex align-items-center">
                            <div class="rounded bg-info  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-filter text-white fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Famille</span>
                                <p class="mb-0 h5 text-black">{{$o_article->famille?->nom ?? '---'}}</p>
                            </div>
                        </div>
                        <div class=" col-xl-3 col-lg-6  my-1  d-flex align-items-center">
                            <div class="rounded bg-success  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-dollar-sign text-white fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Prix de vente</span>
                                <p class="mb-0 h5 text-black">{{$o_article->prix_vente ?? '0'}} MAD</p>
                            </div>
                        </div>
                        <div class=" col-xl-3 col-lg-6  my-1 d-flex align-items-center">
                            <div class="rounded bg-danger  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-coins text-white fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Prix d'achat</span>
                                <p class="mb-0 h5 text-black">{{$o_article->prix_achat ?? '0'}} MAD</p>
                            </div>
                        </div>

                        <div class=" col-xl-3 col-lg-6  my-1   d-flex align-items-center">
                            <div class="rounded bg-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-percent text-white fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Taxe</span>
                                <p class="mb-0 h5 text-black">{{$o_article->taxe ?? '0'}}%</p>
                            </div>
                        </div>

                        <div class=" col-xl-3 col-lg-6 col-12  my-3 d-flex align-items-center">
                            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-warehouse fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Total actuel</span>
                                <p class="mb-0 h5 text-black">{{$magasins->sum('quantite') ?? '0'}}</p>
                            </div>
                        </div>
                        <div class=" col-xl-3 col-lg-6 col-12  my-3 d-flex align-items-center">
                            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fas fa-truck fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Total vendu</span>
                                <p class="mb-0 h5 text-black">{{$magasins->sum('qte_vente') ?? '0'}} </p>
                            </div>
                        </div>
                        <div class=" col-xl-3 col-lg-6 col-12  my-3 d-flex align-items-center">
                            <div class="rounded bg-soft-danger  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fas fa-truck-loading fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Total acheté</span>
                                <p class="mb-0 h5 text-black">{{$magasins->sum('qte_achat') ?? '0'}}</p>
                            </div>
                        </div>
                        <div class=" col-xl-3 col-lg-6 col-12  my-3 d-flex align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fas fa-undo fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Total retour</span>
                                <p class="mb-0 h5 text-black">{{$magasins->sum('qte_retour') ?? '0'}}</p>
                            </div>
                        </div>




                        @if($globals['code_barre'] && !empty($o_article->code_barre) && !empty($codeBarreHtml))
                            <div class="col-xl-3 col-lg-6 col-12 my-3 d-flex align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <span class="font-weight-bolder font-size-sm">Code Barre</span>
                                        <div class="mb-0 h5 text-black">
                                            <!-- Rendre le code barre cliquable -->
                                            <div class="code-barre" onclick="imprimerCodeBarre('{{ $o_article->code_barre }}')">
                                                {!! $codeBarreHtml !!}
                                            </div>
                                            <p class="mt-2 text-center">{{ $o_article->code_barre }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif




                        <h5>Déscription :</h5>
                        <div class="bg-soft-light rounded w-100 h-100 p-3">
                            {{$o_article->description ?? 'Acune déscription'}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h5>Sotck et quantité</h5>
                            <hr class="border">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped " style="border-collapse: collapse !important;">
                                <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Nom</th>
                                    <th>Vendu</th>
                                    <th>Acheté</th>
                                    <th>Retour</th>
                                    <th>Retour d'achat</th>
                                    <th>Quantité</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($magasins as $magasin)
                                    <tr>
                                        <td>{{$magasin->reference}}</td>
                                        <td>{{$magasin->nom}}</td>
                                        <td>{{$magasin->qte_vente}}</td>
                                        <td>{{$magasin->qte_achat}}</td>
                                        <td>{{$magasin->qte_retour}}</td>
                                        <td>{{$magasin->qte_retour_achat}}</td>
                                        <td>{{$magasin->quantite}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection
@push('scripts')

@endpush
<script>
    function imprimerCodeBarre(codeBarre) {
        if (codeBarre) {
            window.open('/articles/imprimer-code-barre/' + codeBarre, '_blank');
        } else {
            alert('Code barre introuvable.');
        }
    }
</script>




