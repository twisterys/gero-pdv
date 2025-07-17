@extends('layouts.main')
@section('document-title','Gestion des documents')
@push('styles')
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{ route('modules.mettre_a_jour') }}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Gestion des documents</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    @method('PUT')
                    <table class="table table-bordered table-striped mt-3 rounded overflow-hidden">
                        <tr>
                            <th>Document</th>
                            <th>Active</th>
                            <th>Payable</th>
                            <th>Action sur stock</th>
                        </tr>
                        @foreach($modules as $module)
                            <tr>
                                <td>
                                    <label class="form-label me-3"
                                           for="module-{{$module->id}}">{{Lang::hasForLocale('ventes.'.$module->type,'fr') ? __('ventes.'.$module->type) : __('achats.'.$module->type)}}</label>
                                </td>
                                <td>
                                    <input name="{{$module->type}}[active]" value="1" type="checkbox" id="module-{{$module->id}}" switch="bool" @checked(old($module->type.'.active',$module->active)) >
                                    <label for="module-{{$module->id}}" data-on-label="Oui" data-off-label="Non"></label>
                                </td>
                                <td>
                                    <select name="{{$module->type}}[payable]" class="form-select"
                                            id="module-stock-{{$module->id}}">
                                        <option @selected(old($module->type.'.stock',$module->action_paiement)==null) value="">Sans</option>
                                        <option @selected(old($module->type.'.stock',$module->action_paiement)=='decaisser') value="decaisser">Décaisser</option>
                                        <option @selected(old($module->type.'.stock',$module->action_paiement)=='encaisser') value="encaisser">Encaisser</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="{{$module->type}}[stock]" class="form-select"
                                            id="module-stock-{{$module->id}}">
                                        <option @selected(old($module->type.'.stock',$module->action_stock)==null) value="">Sans</option>
                                        <option @selected(old($module->type.'.stock',$module->action_stock)=='sortir') value="sortir">Sortir</option>
                                        <option @selected(old($module->type.'.stock',$module->action_stock)=='entrer') value="entrer">Entrer</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
@endpush

