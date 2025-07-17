@extends('layouts.main')
@section('document-title','Forme Juridique')
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
                            <h5 class="m-0">Forme juridique</h5>
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
                            <th>Nom</th>
                            <th>Nom sur facture</th>
                            <th>Active</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($o_formes_juridique as $o_forme_juridique)
                            <tr>
                                <th scope="row">{{ $o_forme_juridique->nom }}</th>
                                <td>{{$o_forme_juridique->nom_sur_facture}}</td>
                                <td>
                                    <div class="form-check-inline d-flex align-items-center">
                                        <input name="{{$o_forme_juridique->id}}[active]" value="1" type="checkbox" id="active-input-edit-{{$o_forme_juridique->id}}"  data-id="{{$o_forme_juridique->id}}"  switch="bool" @if($o_forme_juridique->active) checked @endif>
                                        <label for="active-input-edit-{{$o_forme_juridique->id}}" data-on-label="Oui" data-off-label="Non"></label>
                                    </div>
                                </td>

                                <td>
                                    <a data-url="{{route('formes_juridique.modifier',$o_forme_juridique->id)}}"
                                       data-target="edit-forme-modal" class="__datatable-edit-modal btn btn-sm btn-soft-warning">
                                        <i class="fa fa-pen"></i>
                                    </a>

                                    <a data-url="{{ route('formes_juridique.supprimer', $o_forme_juridique->id) }}"
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

    <div class="modal fade" id="edit-forme-modal" tabindex="-1" aria-labelledby="edit-taxe-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="add-uni-modal" tabindex="-1" aria-labelledby="add-uni-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-uni-modal-title">Ajouter une forme juridique</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post"  id="add-forme-form" action="{{route('formes_juridique.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required " for="nom">forme juridique</label>
                                <input type="text" required class="form-control" id="nom" name="nom">
                                @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label required " for="nom_sur_facture">Nom sur facture</label>
                                <input type="text" required class="form-control" id="nom_sur_facture" name="nom_sur_facture">
                                @error('nom_sur_facture')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check-inline d-flex align-items-center">
                                    <label for="" class="form-check-label me-2">Active</label>
                                    <input name="active" value="1" type="checkbox" id="active-input" switch="bool">
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
        </div>
    </div>


@endsection
@push('scripts')
    <script>
        var submit_add_forme_juridique = !1;

        $('#add-forme-form').submit(function(e) {
            e.preventDefault();

            if (!submit_add_forme_juridique) {
                let spinner = $(__spinner_element);
                let btn = $('#add-uni-modal').find('.btn-primary');
                btn.attr('disabled', '').prepend(spinner);
                submit_add_forme_juridique = true;

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-Token': __csrf_token
                    },
                    success: function(response) {
                        btn.removeAttr('disabled');
                        submit_add_forme_juridique = false;
                        spinner.remove();
                        toastr.success(response);
                        location.reload();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        btn.removeAttr('disabled');
                        submit_add_forme_juridique = false;
                        spinner.remove();
                        toastr.error(xhr.responseText);
                    }
                });
            }
        });

    </script>

    <script>
        $(document).ready(function() {
            $('input[type="checkbox"]').on('change', function() {
                const isChecked = $(this).prop('checked') ? 1 : 0;
                const id = $(this).data('id');

                console.log('ID: ' + id + ', Active: ' + isChecked);

                $.ajax({
                    url: '{{route('formes_juridique.modifier_active')}}',
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

