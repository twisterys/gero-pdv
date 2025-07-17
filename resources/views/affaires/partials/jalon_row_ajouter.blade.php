@php
    $position = $key ?? -1;
@endphp
<tr>


    <td>
{{--        <input type="text" class="form-control id"  name="lignes[{{$position}}][id]" value="{{$ligne['id']?? ($ligne['id'] ?? null)}}">--}}

        <input style="width: 100%;" name="lignes[{{$position}}][nom]"
               class="form-control nom mb-1 {{$errors->has('lignes.'.$position.'.nom')? 'is-invalid' : ''}}" type="text"
               value="{{old('lignes.'.$position.'.nom')}}">
        @error('lignes.'.$position.'.nom')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.nom') }}
        </div>
        @enderror

    </td>

    <td>
        <div class="input-group mb-2">
            <input style="width: 100%;" type="text"
                   class="form-control jalon_date {{$errors->has('lignes.'.$position.'.jalon_date')? 'is-invalid' : ''}}"
                   id="jalon_date" name="lignes[{{$position}}][jalon_date]" readonly required
                   value="{{old('lignes.'.$position.'.jalon_date',Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y'))}}">
{{--                    value="{{ old('date', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">--}}

            @error('lignes.'.$position.'.jalon_date')
            <div class="invalid-feedback">
                {{ $errors->first('lignes.'.$position.'.jalon_date') }}
            </div>
            @enderror
        </div>

    </td>
    <td></td>
</tr>
