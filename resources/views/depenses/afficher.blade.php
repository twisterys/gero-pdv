@extends('layouts.main')
@section('document-title','Détails de dépense')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
@endpush
@section('page')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{route('depenses.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3" style="margin-top: .1rem!important"><i
                                        class="mdi mdi-chart-bell-curve-cumulative me-2 text-success">

                                    </i>
                                    Détails de dépense
                                    <span
                                        class="text-muted opacity-75 font-size-12 text-nowrap text-truncate">({{$o_depense->reference}})</span>
                                </h5>
                            </div>
                            <div class="pull-right">
                                    <button id="paiement-btn"
                                            @if($o_depense->solde != '0' )data-href="{{route('depenses.paiement_modal',$o_depense->id)}}"
                                            @else disabled @endif class="btn btn-soft-info mx-1"><i
                                            class="fa fa-cash-register"></i> <span class="d-none d-sm-block">Payer</span>
                                    </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <div class="row py-3 px-1 mx-0 my-3 rounded">
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 mt-lg-0 mt-3 d-flex align-items-center">
                            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center" style="width: 49px">
                                <i class="fa fa-calendar-alt fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Date de dépense</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_depense->date_operation}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-lg-0 mt-3 d-flex align-items-center">
                            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center" style="width: 49px">
                                <i class="fa fa-money-check fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Nom de dépense </span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_depense->nom_depense ??'-'}}<span class="text-muted font-size-10"></span></p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-lg-0 mt-3 d-flex align-items-center">
                            <div class="rounded bg-soft-danger  p-2 d-flex align-items-center justify-content-center" style="width: 49px">
                                <i class="fa fa-list fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Catégorie </span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_depense->categorie->nom ??'-'}}<span class="text-muted font-size-10"></span></p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-lg-0 mt-3 d-flex align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center" style="width: 49px">
                                <i class="fa fa-user fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm"> Bénéficiaire </span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_depense->pour ??'-'}}<span class="text-muted font-size-10"></span></p>
                            </div>
                        </div>


                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-3 d-flex align-items-md-start">
                            <div class="rounded bg-info text-white  p-2 d-flex align-items-center justify-content-center" style="width: 49px">
                                <i class="fa fa-coins fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Montant</span>
                                <p class="mb-0 h5 text-black text-capitalize"> {{$o_depense->montant}} MAD</p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-3 d-flex align-items-md-start">
                            <div class="rounded bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 49px">
                                <i class="fa fa-cash-register fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Paiement</span>
                                <p class="mb-0 h5 text-black text-capitalize"> {{$o_depense->encaisser}} MAD</p>
                            </div>
                        </div>


                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-3 d-flex align-items-md-start">
                            <div class="rounded bg-danger text-white  p-2 d-flex align-items-center justify-content-center" style="width: 49px">
                                <i class="fa fa-money-bill fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Montant à payer</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_depense->solde}} MAD</p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-3 d-flex align-items-md-start">
                            <div class="rounded bg-warning text-white  p-2 d-flex align-items-center justify-content-center" style="width: 49px">
                                <i class="fa fa-file-invoice-dollar fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Statut de paiement</span>
                                <p class="mb-0 h5 text-black text-capitalize">@lang('ventes.'.$o_depense->statut_paiement)</p>
                            </div>
                        </div>
                    </div>



                    @if($o_depense->description)
                        <div class="col-12 pt-2">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-pen text-success me-3"></i>
                                <h5 class="m-0">Description</h5>
                            </div>
                            <hr class="border">
                            <div class="col-12 rounded p-2" style="background-color: var(--bs-gray-100)">
                                {!! $o_depense->description !!}
                            </div>
                        </div>
                        <br>
                    @endif
                    @if($o_depense )
                        <div class="col-12 pt-2">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-cash-register text-success me-3"></i>
                                <h5 class="m-0">Paiements</h5>
                            </div>
                            <hr class="border">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Compte</th>
                                        <th>Méthode de paiement</th>
                                        <th>Référence de chéque</th>
                                        <th>Date prévu</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                    @forelse($o_depense->paiements as $paiement)
                                        <tr>
                                            <td>{{$paiement->date_paiement}}</td>
                                            <td>{{$paiement->decaisser}} MAD</td>
                                            <td>{{$paiement->compte->nom}}</td>
                                            <td>{{$paiement->methodePaiement->nom}}</td>
                                            <td>{{$paiement->cheque_lcn_reference}}</td>
                                            <td>{{$paiement->cheque_lcn_date}}</td>
                                            <td>{{$paiement->note}}</td>
                                            <td>
                                                <a data-url="{{ route('paiement.afficher',$paiement->id) }}" data-target="paiement-modal"
                                                   class="btn btn-sm btn-primary __datatable-edit-modal">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a data-url="{{ route('paiement.modifier',$paiement->id) }}" data-target="paiement-modal"
                                                   class="btn btn-sm btn-soft-warning __datatable-edit-modal">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form method="post" action="{{route('paiement.supprimer',$paiement->id)}}" class="d-inline">
                                                    @method('delete')
                                                    @csrf
                                                    <button type="button" class="btn btn__delete_paiement btn-soft-danger btn-sm"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="333" >
                                                <p class="text-center m-0 p-0">Aucun paiement</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </table>

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($o_depense)
        <div class="modal fade" id="paiement-modal" tabindex="-1" aria-labelledby="paiement-modal-title"
             aria-hidden="true"
             style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                </div>
            </div>
        </div>
    @endif



@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>


    @if($o_depense)
        <script>
            function checkModal() {
                let methods = ['cheque', 'lcn'];
                if (methods.indexOf($('#method-input').find('option:selected').val()) !== -1) {
                    $('.__variable').removeClass('d-none').find('input').attr('required', '')
                } else {
                    $('.__variable').addClass('d-none').find('input').removeAttr('required')
                }
            }
            var paiement_process = 0;
            $('#paiement-btn').click(function (e) {
                if (paiement_process === 0) {
                    paiement_process = 1;
                    $(this).find('>i').addClass('d-none');
                    let spinner = $(__spinner_element);
                    let btn = $(this);
                    $(this).attr('disabled', '').prepend(spinner);
                    $.ajax({
                        url: $(this).data('href'),
                        success: function (response) {
                            $('#paiement-modal').find('.modal-content').html(response);
                            $('#paiement-modal').modal("show");
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            paiement_process = 0;
                            spinner.remove();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            paiement_process = 0;
                            spinner.remove();
                            if (xhr.status === 403) {
                                toastr.info(xhr.responseText);
                            } else {
                                toastr.error(xhr.responseText);
                            }
                        }
                    })
                }
            });
            $('#paiement-modal').on('show.bs.modal',function (){
                checkModal()
            })
            $(document).on('change','#method-input', function () {
                check()
            })
            check()
            function check() {
                let methods = ['cheque', 'lcn'];
                if (methods.indexOf($('#method-input').find('option:selected').val()) !== -1) {
                    $('.__variable').removeClass('d-none').find('input').attr('required','')
                }else {
                    $('.__variable').addClass('d-none').find('input').removeAttr('required')
                }
            }
            var submit_paiement = !1;
            $(document).on('submit','#paiement_form_edit , #paiement_form',function (e) {
                e.preventDefault();
                if(!submit_paiement){
                    let spinner = $(__spinner_element);
                    let form = $(this)
                    let  btn =form.find('.btn-info');
                    btn.attr('disabled','').prepend(spinner)
                    submit_paiement = !0;
                    $.ajax({
                        url:form.attr('action'),
                        method:'POST',
                        data: $(this).serialize(),
                        headers:{
                            'X-CSRF-Token':__csrf_token
                        },
                        success: function (response) {
                            btn.removeAttr('disabled');
                            submit_paiement = 0;
                            spinner.remove();
                            toastr.success(response);
                            location.reload()
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            btn.removeAttr('disabled');
                            submit_paiement = !1;
                            spinner.remove();
                            toastr.error(xhr.responseText);
                        }
                    })
                }
            })
        </script>
    @endif

    <script>
        $(document).on('click', '.btn__delete_paiement', function (e) {
            e.preventDefault()
            let form = $(this).closest('form');
            Swal.fire({
                title: "Est-vous sûr?",
                text: "voulez-vous supprimer ce paiement ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer!",
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-light mx-2',
                },
                didOpen: () => {
                    $('.btn').blur()
                },
                preConfirm: async () => {
                    form.submit()
                }
            })
        });

    </script>

@endpush
