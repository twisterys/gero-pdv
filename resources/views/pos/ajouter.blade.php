@extends('layouts.main')
@section('document-title', 'Sessions de pos')
@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                        <div>
                            <h5 class="m-0 float-end ms-3"><i class="mdi mdi-cash-register me-2 text-success"></i>
                                Sessions de point de vente
                            </h5>
                        </div>
                    </div>
                    <hr class="border">
                </div>
                <form action="{{ route('pos.commencer') }}" method="post" class="w-100">
                    @csrf
                    <div class="row mt-4">
                        <div class="col-xl-4 col-sm-6 col-12">
                            <select name="magasin_id" id="magasin-select">
                                @foreach ($magasins as $magasin)
                                    <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6  col-12 mt-sm-0 mt-3">
                            <button class="btn btn-primary  ">Commencer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#magasin-select').select2({
            width: '100%',
            minimumResultsForSearch: -1

        })
    </script>
@endpush
