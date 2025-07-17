
@php use App\Models\Vente;@endphp

@extends('layouts.main')
@section('document-title',"Détails d'abonnement" )
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <style>
        .last-col {
            width: 1%;
            white-space: nowrap;
        }
    </style>
@endpush
@section('page')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap">
                            <div>
                                <a href="{{route('abonnements.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="float-end ms-3">
                                    <i class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                    Détails d'abonnements
                                </h5>
                            </div>
                            <div class="pull-right d-md-none d-block">
                                <button class="btn btn-primary actions-mobile" ><i class="fa fa-bars"></i></button>
                            </div>
                            <div class="pull-right d-md-block flex-wrap gap-md-0 gap-2 actions-target-mobile">



                                <a href="{{route('abonnements.modifier', $abonnement->id)}}" class="btn btn-soft-warning mx-1">
                                    <i class="fa fa-edit"></i> Modifier
                                </a>

                                <button id="supprimer-btn" data-url="{{route('abonnements.supprimer',['id'=> $abonnement->id])}}" class="btn btn-danger mx-1">
                                    <i class="fa fa-trash-alt"></i> Supprimer
                                </button>

{{--                                <button id="archiver-btn" class="btn btn-soft-secondary" data-id="{{ $abonnement->id }}">--}}
{{--                                    <i class="fa fa-archive"></i> Archivertttttt--}}
{{--                                </button>--}}



                                <button id="archiver-btn" class="btn btn-soft-secondary" data-id="{{ $abonnement->id }}" data-archived="{{ $abonnement->is_archived ? 1 : 0 }}">
                                    <i class="fa {{ $abonnement->is_archived ? 'fa-undo' : 'fa-archive' }}"></i>
                                    {{ $abonnement->is_archived ? 'Désarchiver' : 'Archiver' }}
                                </button>











                                <div class="dropdown d-inline-block">
                                    <button data-href="{{route('abonnements.renew_modal', $abonnement->id)}}"
                                            id="renew-btn"
                                            type="button"
                                            class="btn btn-soft-info mx-1"><i
                                            class="fa fa fa-calendar-plus "></i> Renouveler

                                    </button>
                                </div>

                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <div class="row py-3 px-1 mx-0 my-3 rounded">
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4   d-flex align-items-center">
                            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-layer-group fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Type</span>
                                <p class="mb-0 h5 text-black">{{$abonnement->article->designation}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-lg-0 mt-3 d-flex align-items-center">
                            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-building fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Client</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$abonnement->client->nom}} </p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-lg-0 mt-3 d-flex align-items-center">
                            <div class="rounded bg-soft-primary  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 47px">
                                <i class="fa fas fa-globe fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Titre</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$abonnement->titre}}</p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-3 d-flex align-items-md-start">
                            <div
                                class="rounded bg-info text-white  p-2 d-flex align-items-center justify-content-center"
                                style="width: 49px">
                                <i class="fa fa-coins fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Prix </span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$abonnement->prix ?? '-'}} MAD</p>
                            </div>
                        </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 mt-lg-0 mt-3 d-flex align-items-center">
                                <div class="rounded bg-success text-white p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-calendar-alt fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span
                                        class="font-weight-bolder font-size-sm">Date d'abonnement</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$abonnement->date_abonnement}}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  mt-lg-0 mt-3  d-flex align-items-center">
                                <div class="rounded bg-danger text-white p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-calendar-alt fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span
                                        class="font-weight-bolder font-size-sm">Date d'expiration</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$abonnement->date_expiration}}</p>
                                </div>
                            </div>
                    </div>


                </div>
            </div>


        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Date de renouvellement</th>
            <th>Date d'expiration</th>
            <th>Montant</th>
            <th>Référence du document</th>
            <th>Note</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($renouvellements as $renouvellement)
            <tr id="renouvellement-{{ $renouvellement->id }}">
                <td>{{ $renouvellement->id }}</td>
                <td>{{ \Carbon\Carbon::parse($renouvellement->date_renouvellement)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($renouvellement->date_expiration)->format('d/m/Y') }}</td>
                <td>{{ $renouvellement->montant }}</td>
                <td>{{ $renouvellement->document_reference }}</td>
                <td>{{ $renouvellement->note }}</td>
                <td>
                    <button class="btn btn-danger" data-url="{{route('abonnements.supprimer_renouvellement',$renouvellement->id)}}"
                            onclick="deleteRenouvellement({{ $renouvellement->id }})">
                        <i class="fa fa-trash"></i> </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Aucun renouvellement trouvé pour cet abonnement.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
                </div></div></div></div>


    <div class="modal fade" id="renew-modal" tabindex="-1" aria-labelledby="renew-modal-title"
         aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

            </div>
        </div>
    </div>

@endsection
@push('scripts')

    <script>
        var renew_process = 0;
        $('#renew-btn').click(function (e) {
            if (renew_process === 0) {
                renew_process = 1;
                $(this).find('>i').addClass('d-none');
                let spinner = $(__spinner_element);
                let btn = $(this);
                $(this).attr('disabled', '').prepend(spinner);
                $.ajax({
                    url: $(this).data('href'),
                    success: function (response) {
                        $('#renew-modal').find('.modal-content').html(response);
                        $('#renew-modal').modal("show");
                        btn.find('>i').removeClass('d-none');
                        btn.removeAttr('disabled');
                        renew_process = 0;
                        spinner.remove();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        btn.find('>i').removeClass('d-none');
                        btn.removeAttr('disabled');
                        renew_process = 0;
                        spinner.remove();
                        if (xhr.status === 403) {
                            if (xhr.status != undefined) {
                                if (xhr.status === 403) {
                                    toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                    return
                                }
                            }
                        }
                        toastr.error(xhr.responseText);
                    }
                })
            }
        });
    </script>
    <script>
        $(document).on('click', '#supprimer-btn', function () {
            Swal.fire({
                title: "Est-vous sûr?",
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer!",
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-soft-danger mx-2', cancelButton: 'btn btn-soft-secondary mx-2',
                },
                didOpen: () => {
                    $('.btn').blur()
                },
                preConfirm: async () => {
                    Swal.showLoading();
                    try {
                        const [response] = await Promise.all([new Promise((resolve, reject) => {
                            $.ajax({
                                url: $(this).data('url'), method: 'DELETE', headers: {
                                    'X-CSRF-TOKEN': __csrf_token
                                }, success: resolve, error: (_, jqXHR) => reject(_)
                            });
                        })]);

                        return response;
                    } catch (jqXHR) {
                        let errorMessage = "Une erreur s'est produite lors de la demande.";
                        if (jqXHR.status === 404) {
                            errorMessage = "La ressource n'a pas été trouvée.";
                        }
                        if (jqXHR.status === 403) {
                            errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                        }
                        Swal.fire({
                            title: 'Erreur',
                            text: errorMessage,
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-danger mx-2',
                            },
                        });
                        throw jqXHR;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        Swal.fire({
                            title: 'Succès',
                            text: result.value,
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-success mx-2',
                            },
                        }).then(result => {
                            if (result.isConfirmed) {
                                window.location.href = "{{route('abonnements.liste')}}";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur',
                            text: "Une erreur s'est produite lors de la demande.",
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-danger mx-2',
                            },
                        });
                    }
                }
            })
        });
    </script>

    <script>
        function deleteRenouvellement(renouvellementId) {
            Swal.fire({
                title: "Est-vous sûr?",
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer!",
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-soft-danger mx-2',
                    cancelButton: 'btn btn-soft-secondary mx-2',
                },
                didOpen: () => {
                    $('.btn').blur()
                },
                preConfirm: async () => {
                    Swal.showLoading();
                    try {
                        const response = await $.ajax({
                            url: `/abonnements/renouvellements/${renouvellementId}`, // Ensure URL is correctly passed with ID
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': __csrf_token
                            }
                        });
                        return response;
                    } catch (jqXHR) {
                        let errorMessage = "Une erreur s'est produite lors de la demande.";
                        if (jqXHR.status === 404) {
                            errorMessage = "La ressource n'a pas été trouvée.";
                        }
                        if (jqXHR.status === 403) {
                            errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                        }
                        Swal.fire({
                            title: 'Erreur',
                            text: errorMessage,
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-danger mx-2',
                            },
                        });
                        throw jqXHR;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        Swal.fire({
                            title: 'Succès',
                            text: 'Renouvellement supprimé avec succès.',
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-success mx-2',
                            },
                        }).then(result => {
                            if (result.isConfirmed) {
                                // Redirect with GET request
                                window.location.href = "{{ route('abonnements.afficher', $abonnement->id) }}";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur',
                            text: "Une erreur s'est produite lors de la demande.",
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-danger mx-2',
                            },
                        });
                    }
                }
            })
        }
    </script>

<script>
//    $(document).on('click', '#archiver-btn', function () {
//     let abonnementId = $(this).data('id');
//     let spinner = $(__spinner_element);
//     let btn = $(this);
//
//     btn.attr('disabled', '').prepend(spinner);
//
//     $.ajax({
//         url: '/abonnements/' + abonnementId + '/archiver',
//         method: 'PUT',
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         success: function (response) {
//             toastr.success(response.message);
//             btn.removeAttr('disabled');
//             spinner.remove();
//             location.reload();
//         },
//         error: function (xhr) {
//             btn.removeAttr('disabled');
//             spinner.remove();
//             if (xhr.status === 403) {
//                 toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
//             } else {
//                 toastr.error("Une erreur s'est produite. Veuillez réessayer.");
//             }
//         }
//     });
// });
$(document).on('click', '#archiver-btn', function () {
    let abonnementId = $(this).data('id');
    let isArchived = $(this).data('archived'); // Récupère l'état actuel
    let url = isArchived ? '/abonnements/' + abonnementId + '/desarchiver' : '/abonnements/' + abonnementId + '/archiver';
    let spinner = $('<span class="spinner-border spinner-border-sm"></span>');
    let btn = $(this);

    btn.attr('disabled', '').prepend(spinner);

    $.ajax({
        url: url,
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            toastr.success(response.message);
            btn.removeAttr('disabled');
            spinner.remove();

            // Inverser l'état et modifier le bouton dynamiquement
            let newIsArchived = isArchived ? 0 : 1;
            btn.data('archived', newIsArchived);
            btn.html(`<i class="fa ${newIsArchived ? 'fa-undo' : 'fa-archive'}"></i> ${newIsArchived ? 'Désarchiver' : 'Archiver'}`);
        },
        error: function (xhr) {
            btn.removeAttr('disabled');
            spinner.remove();
            if (xhr.status === 403) {
                toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
            } else {
                toastr.error("Une erreur s'est produite. Veuillez réessayer.");
            }
        }
    });
});

</script>



@endpush
