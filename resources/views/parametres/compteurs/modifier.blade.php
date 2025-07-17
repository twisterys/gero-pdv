@extends('layouts.main')
@section('document-title','Comptuers')
@push('styles')
    <style>
        tr{
            transition: all ease .6s !important;
        }
    </style>
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{route('compteurs.sauvegarder')}}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Compteurs</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    <div class="row col-12 mx-0 ">
                        <div class="col-12">
                            <table class="table table-bordered table-striped mt-3 rounded overflow-hidden">
                                <tr>
                                    <th>Type</th>
                                    <th>Compteur</th>
                                    <th>Action</th>
                                </tr>
                                @foreach($compteurs as $compteur)
                                    <tr>
                                        <td>{{\App\Models\Compteur::TYPES[$compteur->type]}}</td>
                                        <td>
                                            <span class="switch-edit" >{{$compteur->compteur}}</span>
                                            <div class="switch-edit d-none">
                                                <input type="number" step="1" min="0"  class="form-control" name="{{$compteur->type}}" value="{{old($compteur->type,$compteur->compteur)}}" >
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-soft-warning edit" ><i class="fa fa-edit"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('.edit').on('click',function () {
            $(this).closest('tr').find('.switch-edit').toggleClass('d-none');
            $(this).closest('tr').find('.switch-edit input').focus()
        })
        $('.switch-edit input').on('blur',function () {
            let value = $(this).val();
            $(this).closest('tr').find('span.switch-edit').html(value)
            $(this).closest('tr').find('.switch-edit').toggleClass('d-none');
        })
    </script>
@endpush

