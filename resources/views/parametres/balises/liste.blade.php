@extends('layouts.main')
@section('document-title','Balises')
@push('styles')
@endpush
@section('page')
    <link href="{{asset('libs/spectrum-colorpicker2/spectrum.min.css')}}" rel="stylesheet" type="text/css">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                            <h5 class="m-0">Étiquettes</h5>
                        </div>
                        <div class="page-title-right">
                            <button class="btn btn-soft-success" data-bs-target="#add-tag-modal"
                                    data-bs-toggle="modal"><i class="mdi mdi-plus"></i> Ajouter
                            </button>
                        </div>
                    </div>
                    <hr class="border">
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-centered table-bordered rounded mb-0">
                        <tr>
                            <th>Nom</th>
                            <th>Couleur</th>
                            <th style="width: 1%; white-space: nowrap" >Actions</th>
                        </tr>
                        @forelse($tags as $key=> $o_tag)
                            <tr>
                                <th>{{$o_tag->nom}}</th>
                                <th><div class="badge" style="background: {{$o_tag->couleur}}" >{{$o_tag->couleur}}</div></th>
                                <th>
                                    <div class="d-flex">
                                        <button data-url="{{route('balises.modifier',$o_tag->id)}}"  class="btn btn-sm btn-soft-warning __edit_balise me-2" >
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button data-url="{{route('balises.supprimer',$o_tag->id)}}" class="btn btn-sm btn-soft-danger sa-warning" data-bs-custom-class="danger-tooltip" data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-danger font-size-10"></div></div>'  data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-original-title="Supprimer">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center" >Acunne Étiquette</td>
                            </tr>
                        @endforelse
                    </table>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-tag-modal" tabindex="-1" aria-labelledby="edit-tag-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="add-tag-modal" tabindex="-1" aria-labelledby="add-tag-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-tag-modal-title">Ajouter une balise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="add-tag-form" action="{{route('balises.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="name-input">Nom</label>
                                <input type="text" value="" required class="form-control" id="name-input" name="i_nom">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="couleur-input">Couleur</label>
                                <input type="text"  value="#3b5461" required class="form-control w-100" id="couleur-input" name="i_couleur">
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
    <div class="modal fade" id="edit-tag-modal" tabindex="-1" aria-labelledby="add-tag-modal-title" aria-hidden="true"
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
    <script src="{{asset('libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
    <script>
        var submit_edit_tag = !1;
        $('#add-tag-form').submit(function (e) {
            e.preventDefault();
            if(!submit_edit_tag){
                let spinner = $(__spinner_element);
                let  btn =$('#add-tag-modal').find('.btn-primary');
                btn.attr('disabled','').prepend(spinner)
                submit_edit_tag = !0;
                $.ajax({
                    url:$('#add-tag-form').attr('action'),
                    method:'POST',
                    data: $(this).serialize(),
                    headers:{
                        'X-CSRF-Token':__csrf_token
                    },
                    success: function (response) {
                        btn.removeAttr('disabled');
                        submit_edit_tag = !1;
                        spinner.remove();
                        toastr.success(response);
                        location.reload()
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        btn.removeAttr('disabled');
                        submit_edit_tag = !1;
                        spinner.remove();
                        toastr.error(xhr.responseText);
                    }
                })
            }
        })
        $(document).on('submit','#edit-tag-form',function (e) {
            e.preventDefault();
            if(!submit_edit_tag){
                let spinner = $(__spinner_element);
                let  btn =$('#edit-tag-form').find('.btn-primary');
                btn.attr('disabled','').prepend(spinner)
                submit_edit_tag = !0;
                $.ajax({
                    url:$('#edit-tag-form').attr('action'),
                    method:'POST',
                    data: $(this).serialize(),
                    headers:{
                        'X-CSRF-Token':__csrf_token
                    },
                    success: function (response) {
                        btn.removeAttr('disabled');
                        submit_edit_tag = !1;
                        spinner.remove();
                        toastr.success(response);
                        location.reload()
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        btn.removeAttr('disabled');
                        submit_edit_tag = !1;
                        spinner.remove();
                        toastr.error(xhr.responseText);
                    }
                })
            }
        })
    </script>
    <script>
        $('#add-tag-modal').on('shown.bs.modal', function () {
            $("#couleur-input").spectrum()
        })
        $('#edit-tag-modal').on('shown.bs.modal', function () {
            $("#couleur-input-edit").spectrum()
        })
        $(document).on('click','.__edit_balise',function (){
            let btn = $(this);
            let html = btn.html();
            btn.html(__spinner_element)
           $.ajax({
               url: btn.data('url'),
               success : function (response) {
                   $('#edit-tag-modal .modal-content').html(response);
                   $('#edit-tag-modal').modal('show');
                   btn.html(html)
               }
           })
        });
    </script>
@endpush



