@extends('layouts.main')
@section('document-title','Livraison')
@push('styles')
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">

@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                            <h5 class="m-0">Méthodes de livraison</h5>
                        </div>
                        <div class="page-title-right">
                            <button class="btn btn-soft-success" data-bs-target="#add-uni-modal"
                                    data-bs-toggle="modal"><i class="mdi mdi-plus"></i> Ajouter
                            </button>
                        </div>
                    </div>
                    <hr class="border">
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-centered mb-0">
                        <thead>
                        <tr>
                            <th>Méthode de livraison</th>
                            <th>photo</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($methode_livraisons as $methode_livraison)
                            <tr>
                                <td>{{$methode_livraison->nom}}</td>
                                <td>
                                    <div class="avatar avatar-sm rounded-circle overflow-hidden">
                                        <img src="{{$methode_livraison->image_url()}}" class="img-thumbnail w-100" alt="">
                                    </div>
                                </td>
                                <td>
                                    <a data-url="{{route('methodes-livraison.modifier',$methode_livraison->id)}}" data-target="edit-uni-modal"
                                       class="btn btn-sm btn-soft-warning __datatable-edit-modal">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">il y'a aucune methode de livraison</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add-uni-modal" tabindex="-1" aria-labelledby="add-uni-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-uni-modal-title">Ajouter un méthode de
                        livraison</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('methodes-livraison.sauvegarder')}}" class="needs-validation"
                      enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required " for="nom">Méthode de Livraison</label>
                                <input type="text" required class="form-control" id="nom" name="nom">
                            </div>
                            <div class="col-12 mb-3">
                                <div class="col-12  mb-3 ">
                                    <label for="i_image"
                                           class="form-label {{$errors->has('i_image')? 'is-invalid' : ''}}">Image</label>
                                    <input name="i_image" type="file" id="i_image" accept="image/*">
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
                        <button class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-uni-modal" tabindex="-1" aria-labelledby="edit-uni-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@push('scripts')
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script>
        $("#i_image").dropify({
            messages: {
                default: "Glissez-déposez un fichier ici ou cliquez",
                replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
                remove: "Supprimer",
                error: "Désolé, le fichier trop volumineux",
            },
        });
        $('#edit-uni-modal').on('show.bs.modal',function (){
            $("#i_image_edit").dropify({
                messages: {
                    default: "Glissez-déposez un fichier ici ou cliquez",
                    replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
                    remove: "Supprimer",
                    error: "Désolé, le fichier trop volumineux",
                },
            });
        })
        $(document).on('click','#delete-image',function(){
            $('.mask-on').toggleClass('d-none')
            $('#supprimer_image').val(1);
        })
        @if($errors->any())
            toastr.warning("{{$errors->first()}}")
        @endif
    </script>
@endpush



