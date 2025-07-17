@extends('layouts.main')
@section('document-title','Relance')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
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
                            <h5 class="m-0">Templates de relance</h5>
                        </div>
                        <div class="page-title-right">
                            <a href="{{route('relance.ajouter')}}" class="btn btn-soft-success" ><i class="mdi mdi-plus"></i> Ajouter
                            </a>
                        </div>
                    </div>
                    <hr class="border">
                </div>
                <div class="table-responsive">
                    @foreach(['dv' => 'Devis', 'fa' => 'Facture', 'fp' => 'Facture proforma'] as $type => $label)
                        @if($o_templates->where('type', $type)->isNotEmpty())
                            <h5 class="mt-4">{{ $label }}</h5>
                            <table class="table table-striped table-centered mb-0">
                                <thead>
                                <tr>
                                    <th style="width: 30%;">Nom</th>
                                    <th style="width: 50%;">Active</th>
                                    <th ></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($o_templates->where('type', $type) as $template)
                                    <tr>
                                        <th scope="row">{{ $template->name }}</th>
                                        <td>
                                            <div class="form-check-inline d-flex align-items-center">
                                                <input name="active-switch" value="1" type="checkbox" id="active-input-{{ $template->id }}"
                                                       data-id="{{ $template->id }}" switch="bool"
                                                       @if($template->active) checked @endif>
                                                <label for="active-input-{{ $template->id }}" data-on-label="Oui" data-off-label="Non"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{route('relance.modifier',$template->id)}}"
                                               class="btn btn-sm btn-soft-warning">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                            <a data-url="{{route('relance.supprimer',$template->id)}}"
                                               class="btn btn-sm btn-soft-danger sa-warning">
                                                <i class="fa fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let activeProcess = 0;

        $(document).on('change', 'input[name="active-switch"]', function () {
            if (activeProcess === 0) {
                activeProcess = 1;
                let templateId = $(this).data('id');
                let isChecked = $(this).is(':checked');
                let switchElement = $(this);

                switchElement.prop('disabled', true);

                $.ajax({
                    url: `/parametres/relance/modifier-active/${templateId}`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        active: isChecked ? 1 : 0
                    },
                    success: function (response) {
                        reloadTemplates();
                        toastr.success(response.success);
                    },
                    error: function (xhr) {
                        toastr.warning("Erreur lors de la mise à jour de la template.");
                    },
                    complete: function () {
                        switchElement.prop('disabled', false);
                        activeProcess = 0;
                    }
                });
            }
        });

        function reloadTemplates() {
            $.ajax({
                url: '{{ route('relance.liste') }}',
                method: 'GET',
                success: function (response) {
                    // Remplacer tout le contenu de la table responsive
                    let newTableContent = $(response).find('.table-responsive').html();
                    $('.table-responsive').html(newTableContent);
                },
                error: function () {
                    alert("Une erreur s'est produite lors du rechargement des templates.");
                }
            });
        }
    </script>
@endpush
