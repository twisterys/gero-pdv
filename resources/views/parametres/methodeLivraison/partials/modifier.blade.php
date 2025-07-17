<div class="modal-header">
    <h5 class="modal-title align-self-center" id="add-uni-modal-title">Modifier un méthode de
        livraison</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" action="{{route('methodes-livraison.mettre_a_jour',$o_methode_livraison->id)}}" class="needs-validation"
      enctype="multipart/form-data" novalidate>
    @method('PUT')
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label required " for="nom">Méthode de Livraison</label>
                <input type="text" required class="form-control" value="{{old('nom',$o_methode_livraison->nom)}}" id="nom" name="nom">
            </div>
            <div class="col-12 mb-3">
                <div class="col-12  mb-3 ">
                    <label for="i_image"
                           class="form-label {{$errors->has('i_image')? 'is-invalid' : ''}}">Image</label>
                    @if($o_methode_livraison->image)
                        <div class="col-12  mask-on" >
                            <div class="w-100 overflow-hidden p-3 h-100 d-flex align-items-center justify-content-center rounded bg-light position-relative ">
                                <div class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2" id="delete-image">
                                    <i class="fa fa-trash " ></i>
                                </div>
                                <div class="w-100 overflow-hidden rounded h-100" style="max-height: 250px"> <img src="{{$o_methode_livraison->image_url()}}" class="w-100" alt=""></div>
                            </div>
                        </div>
                        <div class="mask-on d-none">
                            <input name="i_image" type="file" id="i_image_edit" accept="image/*" >
                        </div>
                        <input type="hidden" name="supprimer_image" id="supprimer_image">
                    @else
                        <input name="i_image" type="file" id="i_image_edit" accept="image/*" >
                    @endif

                    <div class="invalid-feedback">
                        @if($errors->has('i_image'))
                            {{ $errors->first('i_image') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-success">Modifier</button>
    </div>
</form>
