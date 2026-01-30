@forelse($articles as $o_article)
    <div class="p-2 col-4 align-items-stretch">
        <div class="article-card border-2 border d-flex flex-column align-items-center rounded overflow-hidden h-100  shadow-sm"
            style="cursor: pointer" data-id="{{ $o_article->id }}" data-nom="{{ $o_article->designation }}"
            data-reference="{{ $o_article->reference }}"
            data-ht="{{ $type == 'vente' ? $o_article->prix_vente : $o_article->prix_achat }}"
            data-unite="{{ $o_article->unite_id }}" data-taxe="{{ $o_article->taxe }}"
            data-quantite-stock="{{ number_format($magasin_id ? $o_article->magasin_stock($magasin_id) : $o_article->quantite, 3, '.', ' ') }}"
        >
            <div class="article-card-header mb-1 w-100 overflow-hidden d-flex align-items-center"
                style="max-height: 100px">
                <img src="{{ $o_article->image ? route('article.image.load', ['file' => $o_article->image]) : 'https://placehold.co/100x100?text=' . $o_article->reference }}"
                    class="w-100" alt="">

            </div>
            <div class="article-card-content w-100 p-1">
                <h6 class="text-capitalize text-center">{{ $o_article->designation }}</h6>
                <p class="m-0 font-size-12 text-center text-muted">{{ $o_article->reference }}</p>
                <p class="m-0 font-size-12 text-center text-muted">
                    {{ number_format($magasin_id ? $o_article->magasin_stock($magasin_id) : $o_article->quantite, 3, '.', ' ') }}
                    {{ $o_article->unite->nom }}</p>
                <p class="m-0 text-center fw-bold my1 text-primary">
                    {{ $type == 'vente' ? number_format($o_article->prix_vente, 3, '.', ' ') : number_format($o_article->prix_achat, 3, '.', ' ') }} MAD</p>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <p class="text-center">
            <i class="fa fa-times fa-2x"></i>
            <br>
            Aucune resultat
            <br>
            <button type="button" data-url="{{ route('articles.ajouter') }}"
                class="add-article-btn btn btn-soft-secondary mt-3"><span class="me-2">+</span> Ajouter une
                article</button>
        </p>
    </div>
@endforelse
