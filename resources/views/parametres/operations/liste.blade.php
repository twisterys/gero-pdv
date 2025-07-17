@extends('layouts.main')
@section('document-title','Opérations')
@push('styles')
    <link href="{{asset('libs/spectrum-colorpicker2/spectrum.min.css')}}" rel="stylesheet" type="text/css">
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                            <h5 class="m-0">Opérations</h5>
                        </div>
                        <div class="page-title-right">
                            <button class="btn btn-soft-success" data-bs-target="#add-operation-modal"
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
                            <th>Action sur compte</th>
                            <th style="width: 1%; white-space: nowrap" >Actions</th>
                        </tr>
                        @forelse($operations as $key=> $o_operation)
                            <tr>
                                <th>{{$o_operation->nom}}</th>
                                <th>{{$o_operation->action === 'encaisser' ? 'Encaisser' : 'Décaisser'}}</th>
                                <th>
                                    <button data-url="{{route('operations.supprimer',$o_operation->id)}}"
                                            class="btn btn-sm btn-soft-danger sa-warning" data-bs-custom-class="danger-tooltip" data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-danger font-size-10"></div></div>'  data-bs-toggle="tooltip" data-bs-placement="top"  data-bs-original-title="Supprimer">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </th>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center" >Acunne operation</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add-operation-modal" tabindex="-1" aria-labelledby="add-operation-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-operation-modal-title">Ajouter une opération</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="add-tag-form" action="{{route('operations.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="i_nom">Nom</label>
                                <input type="text" value="" required class="form-control" id="i_nom" name="i_nom">
                                <div class="invalid-feedback"></div>
                            </div>
{{--                            <div class="col-12 mb-3">--}}
{{--                                <label class="form-label required" for="i_reference">Référence</label>--}}
{{--                                <input type="text" value="" required class="form-control" id="i_reference" name="i_reference">--}}
{{--                                <div class="invalid-feedback"></div>--}}
{{--                            </div>--}}
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="i_action">Action</label>
                                <select name="i_action" class="form-select" id="i_action">
                                    <option value="encaisser">Encaisser</option>
                                    <option value="decaisser">Décaisser</option>
                                </select>
                                <div class="invalid-feedback"></div>
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
    <script src="{{asset('libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
    <script>
        var submit_edit_tag = !1;
        $('#add-tag-form').submit(function (e) {
            e.preventDefault();
            if(!submit_edit_tag){
                let spinner = $(__spinner_element);
                let  btn =$('#add-operation-modal').find('.btn-primary');
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
                        if(xhr.status === 422){
                            let errors = xhr.responseJSON.errors;
                            for (const [key,value] of Object.entries(errors)){
                                $('#'+key).addClass('is-invalid')
                                $('#'+key).siblings('.invalid-feedback').html(value)
                            }
                        }else{
                            toastr.error(xhr.responseText);
                        }
                        spinner.remove();
                    }
                })
            }
        })
    </script>
@endpush



