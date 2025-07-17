@if(in_array('logo',explode(',',$template->elements)))
<div class="col-md-6 mb-3">
    <input type="text" class="filepond-input" name="logo" id="logo-input"
           data-max-width="{{$template->logo_largeur}}"
           data-max-height="{{$template->logo_hauteur}}" data-nom="logo"
           @if($template->logo) data-file="{{addslashes($template->logo)}}" @endif>
</div>
@endif
@if(in_array('image_en_tete',explode(',',$template->elements)))

<div class="col-md-6 mb-3">

    <input type="text" class="filepond-input" name="image_en_tete" id="image-en-tete-input"
           data-max-width="{{$template->image_en_tete_largeur}}"
           data-max-height="{{$template->image_en_tete_hauteur}}" data-nom="entête"
           @if($template->image_en_tete) data-file="{{addslashes($template->image_en_tete)}}" @endif>
</div>
@endif
@if(in_array('image_en_bas',explode(',',$template->elements)))
<div class="col-md-6 mb-3">
    <input type="text" class="filepond-input" name="image_en_bas" id="image-en-bas-input"
           data-max-width="{{$template->image_en_bas_largeur}}"
           data-max-height="{{$template->image_en_bas_hauteur}}" data-nom="bas de page"
           @if($template->image_en_bas) data-file="{{addslashes($template->image_en_bas)}}" @endif>
</div>
@endif
@if(in_array('cachet',explode(',',$template->elements)))
    <div class="col-md-6 mb-3">
    <input type="text" class="filepond-input" name="cachet" id="cachet-input"
           data-max-width="170"
           data-max-height="170"
           data-nom="cachet"
           @if($template->cachet) data-file="{{$template->cachet}}" @endif>
    </div>
@endif
@if(in_array('image_arriere_plan',explode(',',$template->elements)))
<div class="col-md-6 mb-3">
    <input type="text" class="filepond-input" name="image_arriere_plan" id="image-arriere-plan-input"
           data-max-width="794" data-nom="arrière plan"
           data-max-height="1123" data-nom="arrière-plan"
           @if($template->image_arriere_plan) data-file="{{addslashes($template->image_arriere_plan)}}" @endif>
</div>
@endif
<div class="col-md-6 mb-3">
    <label for="coleur-input">Couleur</label>
    <input type="text" name="couleur" value="{{old('couleur',$template->couleur)}}" class="form-control"
           id="coleur-input">
</div>
<div class="col-md-6 mb-md-0 mb-3 d-flex align-items-center justify-content-between">
    <label class="form-label  me-3 " for="afficher_total_en_chiffre">Afficher le total en
        chiffre</label>
    <input name="afficher_total_en_chiffre" value="1" type="checkbox" id="afficher_total_en_chiffre"
           switch="bool" @checked(old('afficher_total_en_chiffre',$template->afficher_total_en_chiffre))>
    <label for="afficher_total_en_chiffre" data-on-label="Oui" data-off-label="Non"></label>
</div>
