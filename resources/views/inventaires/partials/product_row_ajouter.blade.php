@php
    $position = $key ?? -1;
@endphp
<tr>
    <input type="hidden" class="article_id" name="lignes[{{$position}}][i_article_id]" value="{{old('lignes.'.$position.'.i_article_id')}}">
    <input type="hidden" class="article_reference" name="lignes[{{$position}}][i_article_reference]" value="{{old('lignes.'.$position.'.i_article_reference')}}">
    <td>
        <div class="input-group mb-2" >
            @if(old('lignes.'.$position.'.i_article_reference'))
                <span class="input-group-text" >{{old('lignes.'.$position.'.i_article_reference')}}</span>
            @endif
            <input readonly type="text" name="lignes[{{$position}}][i_article]"
                   class="form-control  {{$errors->has('lignes.'.$position.'.i_article')? 'is-invalid' : ''}}"
                   value="{{old('lignes.'.$position.'.i_article')}}">
            <button type="button" class="btn btn-soft-primary article_btn"  ><i class="fa fa-store" ></i> </button>
        </div>
        @error('lignes.'.$position.'.i_article')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.i_article') }}
        </div>
        @enderror
    </td>

    <td>
        <input readonly class="form-control quantite_stock mb-1 {{$errors->has('lignes.'.$position.'.quantite_stock')? 'is-invalid' : ''}}"
               name="lignes[{{$position}}][quantite_stock]" type="number"
               value="{{old('lignes.'.$position.'.quantite_stock')}}">
        @error('lignes.'.$position.'.quantite_stock')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.quantite_stock') }}
        </div>
        @enderror
    </td>
    <td>
        <input   class="form-control quantite_new mb-1 {{$errors->has('lignes.'.$position.'.quantite_new')? 'is-invalid' : ''}}"
               name="lignes[{{$position}}][quantite_new]" type="number"
               value="{{old('lignes.'.$position.'.quantite_new')}}">
        @error('lignes.'.$position.'.quantite_new')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.quantite_new') }}
        </div>
        @enderror
    </td>


    <td>
    </td>
</tr>
