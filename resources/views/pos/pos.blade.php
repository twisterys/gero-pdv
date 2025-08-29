@extends('layouts.pos_main')
@section('document-title', 'Point de vente')
@push('styles')
    <style>
        .table-cell {
            padding: .5rem;
        }

        table.table {
            border-collapse: collapse !important;
        }

        #main-row > * {
            padding: .3rem !important;
        }

        #main-row .card {

            margin-bottom: 0;
        }

        .history-tab.active {
            background-color: white !important;
            color: var(--bs-primary) !important;
            border: white !important;
            position: relative;
        }

        .history-tab.active::after {
            content: " ";
            position: absolute;
            top: 50%;
            width: 100%;
            left: 0;
            height: 50px;
            z-index: -1;
            background-color: white;
        }
    </style>
@endpush
@section('page')
    <div id="root" style="height: calc(100vh - 5rem);overflow:hidden"></div>
@endsection
@push('scripts')
    <script>
        const __default_client = {!! $client ?? 'null' !!};
        const __comptes = {!! $comptes !!};
        const __methodes = {!! $methodes !!};
        const __magasins = {!! $magasins !!};
        const __formes = {!! $formes_juridique !!};
        const __api_url = '{{ url('api/v-' . $pos_type) }}/';
        const __session_id = '{{ $session->id }}';
        const __magasin_ref = '{{ $session->magasin->reference }}';
        const __ouverture = '{{$ouverture}}'
        const __prixModification = '{{$modifier_prix}}'
        const __depenses = {!! $depenses !!};
        const __is_code_barre = '{{$is_code_barre}}'
        const __is_price_editable = '{{$is_price_editable}}'
        const __on_reduction = '{{$on_reduction}}'
        const __is_depenses='{{$is_depenses}}'
        const __is_historique='{{$is_historique}}'
        const __is_demandes='{{$is_demandes}}'
        const __rapport_ac_enabled='{{$rapport_ac_enabled}}'
        const __rapport_as_enabled='{{$rapport_as_enabled}}'
        const __rapport_af_enabled='{{$rapport_af_enabled}}'
        const __rapport_cr_enabled='{{$rapport_cr_enabled}}'
        const __rapport_tr_enabled='{{$rapport_tr_enabled}}'
        const __vendeur_nom = '{{ auth()->user()->name }}'
        $(document).on('show.bs.modal', function () {
            $('.tooltip').remove();
        })
        sessionStorage.setItem('csrf', '{!! csrf_token() !!}')
        sessionStorage.setItem('access_token', '{!! auth()->user()->createToken('auth-api')->plainTextToken !!}')
    </script>
    @viteReactRefresh
    @vite(['resources/js/components/pages/Pos' . ucfirst($pos_type) . '.jsx'])
@endpush
