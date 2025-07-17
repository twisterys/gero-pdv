<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Selectioner une article</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <label for="" class="form-label">Rechercher</label>
    <div class="input-group">
        <input type="text" name="search" class="form-control" id="article_modal_search_input" data-url="{{route('articles.modal_recherche',$type)}}" placeholder="Rechercer par nom ou référence"
               autocomplete="off">
        <button type="button" data-url="{{route('articles.ajouter')}}" class="btn btn-soft-secondary add-article-btn">+</button>
    </div>
    <div class="mt-3 row p-2 align-items-stretch" id="__result">
        @include('transferts.partials.modal_content')
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
    <button class="btn btn-primary" type="button" id="confirm_article">Valider</button>
</div>

