@php
    if (!isset($attrs)) {
        $attrs = [];
    }
@endphp
@if (isset($show))
    <a href="{{ route($crudRoutePart . '.' . $show, [$id, ...$attrs]) }}" class="btn btn-sm btn-soft-primary"
        data-bs-toggle="tooltip" data-bs-custom-class="primary-tooltip" data-bs-placement="top"
        data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-primary font-size-10"></div></div>'
        data-bs-original-title="Consulter">
        <i class="fa fa-eye"></i>
    </a>
@endif

@if (isset($show_modal))
    <a data-url="{{ $show_modal['url'] }}" data-target="{{ $show_modal['modal_id'] }} "
       class="btn btn-sm btn-primary __datatable-edit-modal">
        <i class="fa fa-eye"></i>
    </a>
@endif

@if (isset($edit))
    <a href="{{ route($crudRoutePart . '.' . $edit, [$id, ...$attrs]) }}" class="btn btn-sm btn-soft-warning "
        data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-warning font-size-10"></div></div>'
        data-bs-toggle="tooltip" data-bs-custom-class="bg-warning" data-bs-placement="top"
        data-bs-original-title="Modifier">
        <i class="fa fa-edit"></i>
    </a>
@endif
@if (isset($connexion))
    <a href="{{ route($crudRoutePart .'.'. $connexion, [$id, ...$attrs]) }}" class="btn btn-sm btn-soft-info">
        <i class="mdi mdi-login "></i>
    </a>
@endif
@if (isset($edit_modal))
    <a data-url="{{ $edit_modal['url'] }}" data-target="{{ $edit_modal['modal_id'] }} "
        class="btn btn-sm btn-soft-warning __datatable-edit-modal">
        <i class="fa fa-edit"></i>
    </a>
@endif
@if (isset($delete))
    <button data-url="{{ route($crudRoutePart . '.' . $delete, [$id, ...$attrs]) }}"
        class="btn btn-sm btn-soft-danger sa-warning" data-bs-custom-class="danger-tooltip"
        data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-danger font-size-10"></div></div>'
        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Supprimer">
        <i class="fa fa-trash-alt"></i>
    </button>
@endif
@if (isset($paiement))
    <a data-url="{{ route('paiements.' . $paiement, [$id, ...$attrs]) }}" class="btn btn-sm btn-soft-info">
        <i class="fas fa-coins"></i>
    </a>
@endif

@if (isset($rollback))
    <button data-url="{{ route($crudRoutePart . '.' . $rollback, [$id, ...$attrs]) }}"
            class="btn btn-sm btn-soft-danger rollback" data-bs-custom-class="warning-tooltip"
            data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-danger font-size-10"></div></div>'
            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Rollback">
        <i class="fa fa-undo-alt"></i>
    </button>
@endif



