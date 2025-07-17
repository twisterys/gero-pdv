@extends('layouts.main')
@section('document-title',"Afficher une transformation")
@push('styles')
@endpush
@section('page')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap">
                            <div>
                                <a href="{{route('transformations.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3" style="margin-top: .1rem!important"><i
                                        class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                    Afficher une transformation <span
                                        class="text-muted opacity-75 font-size-12 text-nowrap text-truncate">{{$o_transformation->objet? '('.$o_transformation->objet.')': null}}</span>
                                </h5>
                            </div>
                            <div class="pull-right d-md-none d-block">
                                <button class="btn btn-primary actions-mobile"><i class="fa fa-bars"></i></button>
                            </div>
                            <div class="pull-right d-md-block flex-wrap gap-md-0 gap-2 actions-target-mobile">
                                @can('transformation.annuler')
                                    @if($o_transformation->status === 'transformé')
                                        <button data-url="{{route('transformations.annuler',$o_transformation->id)}}"
                                                id="annuler-btn" class="btn btn-soft-success mx-1"><i
                                                class="fa fa-times"></i> Annuler
                                        </button>
                                    @endif
                                @endcan
                                @can('transformation.supprimer')
                                    @if($o_transformation->statut !== 'transformé')
                                        <button id="supprimer-btn"
                                                data-url="{{route('transformations.supprimer', $o_transformation->id)}}"
                                                class="btn btn-danger mx-1"><i
                                                class="fa fa-trash-alt"></i> Supprimer
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <div class="d-md-flex flex-wrap d-none py-3 px-1 mx-0 my-3 rounded more-target">
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 my-2  col-12  d-flex align-items-center">
                            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-id-card fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Reference</span>
                                <p class="mb-0 h5 text-black">{{$o_transformation->reference??'-'}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  col-12  my-2 d-flex align-items-center">
                            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-store fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Magasin</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_transformation->magasin->nom}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2  d-flex align-items-center">
                            <div class="rounded bg-soft-danger p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-calendar-alt fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Date</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_transformation->date}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-star fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Statut</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_transformation->status}}</p>
                            </div>
                        </div>
                        @if($o_transformation->objet)
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-center">
                                <div
                                    class="rounded bg-soft-primary  p-2 d-flex align-items-center justify-content-center"
                                    style="width: 49px">
                                    <i class="fa fa-info-circle fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Objet</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$o_transformation->objet}}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row mx-0">
                        <div class="col-6 mt-4 tt">
                            <div class="w-100">
                                <h5 class="text-muted">
                                    Articles sortants</h5>
                                <hr class="border border-success">
                            </div>
                            <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                    <thead>
                                    <tr class="bg-primary text-white ">
                                        {{-- <th>Reference</th> --}}
                                        <th class="text-white ">Article</th>
                                        <th class="text-white" style="width: 1%;white-space: nowrap;">Quantité</th>
                                    </tr>
                                    </thead>
                                    <!-- The tbody tag will be populated by JavaScript -->
                                    <tbody id="lignes_sortant">
                                    @foreach($o_transformation->lignes->where('type','sortant')->all() as $ligne)
                                        <tr>
                                            <td>{{$ligne->nom_article}}</td>
                                            <td>{{$ligne->quantite}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="col-6 mt-4 tt">
                            <div class="w-100">
                                <h5 class="text-muted">
                                    Articles entrants</h5>
                                <hr class="border border-success">
                            </div>
                            <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                    <thead>
                                    <tr class="bg-primary text-white ">
                                        {{-- <th>Reference</th> --}}
                                        <th class="text-white ">Article</th>
                                        <th class="text-white" style="width: 1%;white-space: nowrap;">Quantité</th>
                                    </tr>
                                    </thead>
                                    <!-- The tbody tag will be populated by JavaScript -->
                                    <tbody id="lignes_sortant">
                                    @foreach($o_transformation->lignes->where('type','entrant')->all() as $ligne)
                                        <tr>
                                            <td>{{$ligne->nom_article}}</td>
                                            <td>{{$ligne->quantite}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($o_transformation->note)
                            <div class="col-12 pt-2">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-pen text-success me-3"></i>
                                    <h5 class="m-0">Note</h5>
                                </div>
                                <hr class="border">
                                <div class="col-12 rounded p-2" style="background-color: var(--bs-gray-100)">
                                    {!! $o_transformation->note !!}
                                </div>
                            </div>
                        @endif
                    </div>


                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
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
                                window.location.href = "{{route('transformations.afficher',$o_transformation->id)}}";
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
        $(document).on('click', '#annuler-btn', function () {
            Swal.fire({
                title: "Est-vous sûr?",
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, annuler !",
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
                                url: $(this).data('url'), method: 'PUT', headers: {
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
                                window.location.href = "{{route('transformations.afficher',$o_transformation->id)}}";
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
@endpush
