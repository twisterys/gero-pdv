@php
    $position = $key ?? -1;
@endphp
<tr>
    <input type="hidden" class="article_id" name="lignes[{{$position}}][i_article_id]" value="{{old('lignes.'.$position.'.i_article_id')}}">
    <input type="hidden" class="article_reference" name="lignes[{{$position}}][i_article_reference]" value="{{old('lignes.'.$position.'.i_article_reference')}}">
    <td>
        <div class="input-group mb-2">
            @if(old('lignes.'.$position.'.i_article_reference'))
                <span class="input-group-text" >{{old('lignes.'.$position.'.i_article_reference')}}</span>
            @endif
            <input type="text" name="lignes[{{$position}}][i_article]"
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
        <input style="width: 120px" class="form-control quantite mb-1 {{$errors->has('lignes.'.$position.'.i_quantite')? 'is-invalid' : ''}}" step="0.01"
               name="lignes[{{$position}}][i_quantite]" type="number"
               value="{{old('lignes.'.$position.'.i_quantite')}}">
        @error('lignes.'.$position.'.i_quantite')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.i_quantite') }}
        </div>
        @enderror

    </td>
    <td>
    </td>


</tr>
