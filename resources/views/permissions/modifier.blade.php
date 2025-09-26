@extends('layouts.main')
@section('document-title','Roles')
@push('styles')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
    <link href="{{asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('libs/daterangepicker/css/daterangepicker.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
    <link rel="stylesheet" href="{{asset('libs/spectrum-colorpicker2/spectrum.min.css')}}">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="articles-form" enctype="multipart/form-data" action="{{route('permissions.mettre_a_jour',$o_role->id)}}"
                          method="post" autocomplete="off">
                        @method('PUT')
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div id="__fixed" class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{route('permissions.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="fa  fas fa-boxes me-2 text-success"></i>
                                        Modifier {{$o_role->name}}</h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>
                        @csrf
                        <div class="row" >
                            <div class="col-md-3 col-sm-6 col-12">
                                <label for="nom" class="form-label">Nom</label>
                                <input name="nom" id="nom" type="text" class="form-control @error('nom') is-invalid @enderror " value="{{old('nom',$o_role->name)}}">
                                @error('nom')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <h5 class="mt-3">Permissions</h5>
                            <hr class="border">
                            <div class="row col-12" id="permissions">
                                @foreach($wild_cards as $card)
                                    @if(in_array($card,['utilisateur','pos','commercial','transfert_stock','stock','transformation']))
                                        @php
                                            $array_limite = ['utilisateur'=>'users','pos'=>'pos','commercial'=>'commerciaux','transfert_stock'=>'stock','stock'=>'stock','transformation'=>'transformation']
                                        @endphp
                                        @if(\App\Services\LimiteService::is_enabled($array_limite[$card]))
                                            <div class="col-md-4 col-sm-6 col-12 mt-3">
                                                <div class="card shadow-sm h-100">
                                                    <div class="card-header">
                                                        <div
                                                            class="card-title m-0 d-flex justify-content-between align-items-center">
                                                            <h4 class="m-0">@lang('permissions.'.$card)</h4>
                                                            <div class="d-flex align-items-center">
                                                                <input name="{{$card}}" value="1" type="checkbox" @checked(old($card)|| in_array($card,$o_role_permissions) )  class="wild-card" id="{{$card}}"
                                                                       switch="bool">
                                                                <label for="{{$card}}" data-on-label="" data-off-label=""></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body ">
                                                        @foreach($permissions->filter(function ($item) use($card) {
                                                                            return false !== stristr(explode('.',$item->name)[0], $card) && !str_contains($item->name,'.*') ;
                                                                                                            }) as $permission)
                                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                                <label for="{{$permission->name}}" class="h4 m-0">@lang('permissions.'.$permission->name)</label>
                                                                <input name="{{str_replace('.','__',$permission->name)}}" value="1" type="checkbox"   class="child-permission"
                                                                       id="{{$permission->name}}"
                                                                       switch="bool"  @checked(old($permission->name)|| in_array($permission->name,$o_role_permissions)) >
                                                                <label for="{{$permission->name}}" data-on-label=""
                                                                       data-off-label=""></label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    @else
                                    <div class="col-md-4 col-sm-6 col-12 mt-3">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header">
                                                <div
                                                    class="card-title m-0 d-flex justify-content-between align-items-center">
                                                    <h4 class="m-0">@lang('permissions.'.$card)</h4>
                                                    <div class="d-flex align-items-center">
                                                        <input name="{{$card}}" value="1" type="checkbox" @checked(old($card)|| in_array($card,$o_role_permissions) )  class="wild-card" id="{{$card}}"
                                                               switch="bool">
                                                        <label for="{{$card}}" data-on-label="" data-off-label=""></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body ">
                                                @foreach($permissions->filter(function ($item) use($card) {
                                                                    return false !== stristr(explode('.',$item->name)[0], $card) && !str_contains($item->name,'.*') ;
                                                                                                    }) as $permission)
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <label for="{{$permission->name}}" class="h4 m-0">@lang('permissions.'.$permission->name)</label>
                                                        <input name="{{str_replace('.','__',$permission->name)}}" value="1" type="checkbox"   class="child-permission"
                                                               id="{{$permission->name}}"
                                                               switch="bool"  @checked(old($permission->name)|| in_array($permission->name,$o_role_permissions)) >
                                                        <label for="{{$permission->name}}" data-on-label=""
                                                               data-off-label=""></label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                @endforeach

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{asset('libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
    <script src="{{asset('libs/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('libs/daterangepicker/js/daterangepicker.js')}}"></script>
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script>
        $('.wild-card').on('change',function (){
            if($(this).is(':checked')){
                $(this).closest('.card').find('.card-body input').prop('checked',true)
            }else {
                $(this).closest('.card').find('.card-body input').prop('checked',false)
            }
        })
        $('.child-permission').on('change',function (){
            if($(this).is(':checked')){
                if (!$(this).closest('.card-body').find('.child-permission').not(':checked').length){
                    $(this).closest('.card').find('.wild-card').prop('checked',true)
                }
            }else {
                $(this).closest('.card').find('.wild-card').prop('checked',false)
            }
        })
        let sorted =  $('#permissions').children().sort(function (a,b){
            var vA = $('.card-body', a).find('.child-permission').length;
            var vB = $('.card-body', b).find('.child-permission').length;
            return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
        });
        $('#permissions').append(sorted)
    </script>
@endpush
