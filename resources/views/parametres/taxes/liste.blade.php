@extends('layouts.main')
@section('document-title','Taxes')
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
                            <h5 class="m-0">Taxes</h5>
                        </div>
                        <div class="page-title-right">
                            <button class="btn btn-soft-success" data-bs-target="#add-taxe-modal"
                                    data-bs-toggle="modal"><i class="mdi mdi-plus"></i> Ajouter
                            </button>
                        </div>
                    </div>
                    <hr class="border">
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-centered table-bordered rounded mb-0">
                        <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Valeur</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($taxes as $key=> $o_taxe)
                            <tr>
                                <th>{{$o_taxe->nom}}</th>
                                <th>{{$o_taxe->valeur}}</th>
                                <th>
                                    <div class="form-check-inline d-flex align-items-center">
                                        <input name="{{$o_taxe->valeur}}[i_par_defaut]" value="1" type="checkbox" id="active-input-edit-{{$o_taxe->valeur}}"  data-valeur="{{$o_taxe->valeur}}"  switch="bool" @if($o_taxe->active) checked @endif>
                                        <label for="active-input-edit-{{$o_taxe->valeur}}" data-on-label="Oui" data-off-label="Non"></label>
                                    </div>

                                </th>
                                <th>
                                    <a data-url="{{route('taxes.modifier',$o_taxe->valeur)}}" data-target="edit-taxe-modal"
                                       class="__datatable-edit-modal btn btn-sm btn-soft-warning">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <button data-url="{{route('taxes.supprimer',$o_taxe->valeur)}}" class="btn btn-sm btn-soft-danger sa-warning" data-bs-custom-class="danger-tooltip" data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-danger font-size-10"></div></div>'  data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-original-title="Supprimer">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-taxe-modal" tabindex="-1" aria-labelledby="edit-taxe-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="add-taxe-modal" tabindex="-1" aria-labelledby="add-taxe-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-taxe-modal-title">Ajouter une taxe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="add-taxe-form" action="{{route('taxes.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="name-input">Nom</label>
                                <input type="text" value="" required class="form-control" id="name-input" name="i_nom">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="valeur-input">Valeur</label>
                                <div class="input-group">
                                    <input type="text" value="" required class="form-control" id="valeur-input" name="i_valeur">
                                    <span class="input-group-text" >%</span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check-inline d-flex align-items-center">
                                    <label for="" class="form-check-label me-2" >Active</label>
                                    <input name="i_par_defaut" value="1" type="checkbox" id="active-input-edit" switch="bool">
                                    <label for="active-input-edit" data-on-label="Oui" data-off-label="Non"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@push('scripts')
    <script>
        var submit_edit_taxe = !1;
        $('#add-taxe-form').submit(function (e) {
            e.preventDefault();
            if(!submit_edit_taxe){
                let spinner = $(__spinner_element);
                let  btn =$('#add-taxe-modal').find('.btn-primary');
                btn.attr('disabled','').prepend(spinner)
                submit_edit_taxe = !0;
                $.ajax({
                    url:$('#add-taxe-form').attr('action'),
                    method:'POST',
                    data: $(this).serialize(),
                    headers:{
                        'X-CSRF-Token':__csrf_token
                    },
                    success: function (response) {
                        btn.removeAttr('disabled');
                        submit_edit_taxe = !1;
                        spinner.remove();
                        toastr.success(response);
                        location.reload()
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        btn.removeAttr('disabled');
                        submit_edit_taxe = !1;
                        spinner.remove();
                        toastr.error(xhr.responseText);
                    }
                })
            }
        })
    </script>

    <script>
        $(document).ready(function() {
            $('input[type="checkbox"]').on('change', function() {
                const isChecked = $(this).prop('checked') ? 1 : 0;
                const valeur = $(this).data('valeur');

                console.log('ID: ' + valeur + ', Active: ' + isChecked);

                $.ajax({
                    url: '{{route('taxes.modifier_active')}}',
                    method: 'POST',
                    dataType: 'json',
                    headers:{
                        'X-CSRF-Token':__csrf_token
                    },
                    data: {
                        valeur: valeur,
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



