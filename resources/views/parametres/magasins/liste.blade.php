@extends('layouts.main')
@section('document-title','Magasins')
@push('styles')

@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                            <h5 class="m-0">Magasins</h5>
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
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Nom</th>
                            {{--                <th>Adresse</th>--}}
                            <th>Type</th>
                            <th>Actif</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($magasins as $magasin)
                            <tr>
                                <th scope="row">{{ $magasin->reference }}</th>
                                <td> {{ $magasin->nom }}   </td>
                                {{--                    <td> {{ $magasin->adresse }}   </td>--}}
                                <td>{{$magasin->type_local_name}}</td>
                                <td>

                                    <div class="form-check-inline d-flex align-items-center">
                                        <input name="{{$magasin->id}}[i_par_defaut]" value="1" type="checkbox" id="active-input-edit-{{$magasin->id}}"  data-id="{{$magasin->id}}"  switch="bool" @if($magasin->active) checked @endif>
                                        <label for="active-input-edit-{{$magasin->id}}" data-on-label="Oui" data-off-label="Non"></label>
                                    </div>

                                </td>
                                <td>
                                    <a  data-url="{{route('magasin.modifier',$magasin->id)}}" data-target="edit-uni-modal"
                                        class="__datatable-edit-modal btn btn-sm btn-soft-warning">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <a data-url="{{route('magasin.supprimer',$magasin->id)}}"
                                       class="btn btn-sm btn-soft-danger sa-warning">
                                        <i class="fa fa-trash-alt"></i>
                                    </a>
                                </td>


                            </tr>
                        @endforeach
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
                    <h5 class="modal-title align-self-center" id="add-uni-modal-title">Ajouter un magasin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('magasin.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required " for="reference">Référence</label>
                                <input value="{{$reference}}" type="text" required class="form-control" id="reference" name="reference">
                                {{--                                <div class="invalid-feedback">Veuillez d'abord entrer un méthode de paiement</div>--}}
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required " for="nom">Nom</label>
                                <input type="text" required class="form-control" id="nom" name="nom">
                                {{--<div class="invalid-feedback">Veuillez d'abord entrer un méthode de paiement</div>--}}
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required " for="adresse">Adresse</label>
                                <input type="text" required class="form-control" id="adresse" name="adresse">
                                {{--<div class="invalid-feedback">Veuillez d'abord entrer un méthode de paiement</div>--}}
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="type_local">Type</label>
                                <select class="form-select" id="type_local" name="type_local" data-parsley-multiple="groups" data-parsley-mincheck="1">
                                    <option value="1" selected>Point de vente & dépôt</option>
                                    <option value="2">Dépôt seulement</option>
                                </select>
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
    <script>
        const checkboxes = document.querySelectorAll('.form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    if (cb !== this) {
                        cb.checked = false;
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('input[type="checkbox"]').on('change', function() {
                const isChecked = $(this).prop('checked') ? 1 : 0;
                const id = $(this).data('id');
                console.log('ID: ' + id + ', Active: ' + isChecked);
                $.ajax({
                    url: '{{route('magasin.modifier_active')}}',
                    method: 'POST',
                    dataType: 'json',
                    headers:{
                        'X-CSRF-Token':__csrf_token
                    },
                    data: {
                        id: id,
                        active: isChecked
                    },
                    success: function(response) {
                        console.log('Données mises à jour avec succès');
                    },
                    error: function(error) {
                        console.log('Erreur lors de la mise à jour des données');
                    }
                });
            });
        });
    </script>
@endpush

