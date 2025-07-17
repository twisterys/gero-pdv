@extends('layouts.main')
@section('document-title','Unités')
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
                            <h5 class="m-0">Unités</h5>
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
                            <th>Unité</th>
                            <th>Active</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($i_unite as $unite)
                            <tr>
                                <th scope="row">{{ $unite->nom }}</th>
                                <td>

                                    <div class="form-check-inline d-flex align-items-center">
                                        <input name="{{$unite->id}}[i_par_defaut]" value="1" type="checkbox" id="active-input-edit-{{$unite->id}}"  data-id="{{$unite->id}}"  switch="bool" @if($unite->active) checked @endif>
                                        <label for="active-input-edit-{{$unite->id}}" data-on-label="Oui" data-off-label="Non"></label>
                                    </div>

                                </td>
                                <td>
                                    <a data-url="{{route('unites.modifier',$unite->id)}}" data-target="edit-uni-modal"
                                       class="__datatable-edit-modal btn btn-sm btn-soft-warning">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <a data-url="{{route('unites.supprimer',$unite->id)}}"
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
    <div class="modal fade" id="edit-uni-modal" tabindex="-1" aria-labelledby="edit-uni-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <div class="modal fade" id="add-uni-modal" tabindex="-1" aria-labelledby="add-uni-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-uni-modal-title">Ajouter une unité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('unites.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required " for="unite-input">Unité</label>
                                <input type="text" required class="form-control" id="unite-input" name="i_nom">
                                <div class="invalid-feedback">Veuillez d'abord entrer une unité</div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check-inline d-flex align-items-center">
                                    <label for="" class="form-check-label me-2">Par défaut</label>
                                    <input name="i_default" value="1" type="checkbox" id="active-input" switch="bool">
                                    <label for="active-input" data-on-label="Oui" data-off-label="Non"></label>
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
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal-dialog -->
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('input[type="checkbox"]').on('change', function() {
                const isChecked = $(this).prop('checked') ? 1 : 0;
                const id = $(this).data('id');

                console.log('ID: ' + id + ', Active: ' + isChecked);

                $.ajax({
                    url: '{{route('unites.modifier_active')}}',
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

