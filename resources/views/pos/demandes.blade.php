@extends('layouts.pos_main')
@section('document-title', 'Demandes')
@push('styles')
    <style>
        .table-cell {
            padding: .5rem;
        }

        table.table {
            border-collapse: collapse !important;
        }

        #main-row>* {
            padding: .3rem !important;
        }

        #main-row .card {

            margin-bottom: 0;
        }
    </style>
@endpush
@section('page')
    <div id="root" style="height: calc(100vh - 5rem);overflow:hidden"></div>
@endsection
@push('scripts')
    <script>
        $(document).on('show.bs.modal',function (){
            $('.tooltip').remove();
        })
        const __session_id = '{{ $session->id }}';
        const __magasin_ref = '{{ $session->magasin->reference }}';
        const __magasins = {!! $magasins !!};
        const __api_url = '{{ url('api/v-' . $pos_type) }}/';
        sessionStorage.setItem('csrf','{!! csrf_token() !!}')
        sessionStorage.setItem('access_token','{!! auth()->user()->createToken('auth-api')->plainTextToken !!}')
    </script>
    @viteReactRefresh
    @vite(['resources/js/components/pages/Demandes.jsx'])
@endpush
