@php
    $position = -1;
  if(isset($key)){
        $position= $key;
    }
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
        <textarea name="lignes[{{$position}}][i_description]"
                  class="summernote {{$errors->has('lignes.'.$position.'.i_description')? 'is-invalid' : ''}}
                  ">{{old('lignes.'.$position.'.i_description')}}</textarea>
            @error('lignes.'.$position.'.i_description')
            <div class="invalid-feedback">
                {{ $errors->first('lignes.'.$position.'.i_description') }}
            </div>
            @enderror
    </td>
    <td>
        <input style="width: 120px" class="form-control quantite mb-1 {{$errors->has('lignes.'.$position.'.i_quantite')? 'is-invalid' : ''}}"
               name="lignes[{{$position}}][i_quantite]" type="number"
               value="{{old('lignes.'.$position.'.i_quantite')}}">
        @error('lignes.'.$position.'.i_quantite')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.i_quantite') }}
        </div>
        @enderror
        <select class="form-select row_select2 unite {{$errors->has('lignes.'.$position.'.i_unite')? 'is-invalid' : ''}}" style="width: 120px" name="lignes[{{$position}}][i_unite]" id="">
            @foreach($o_unites as $o_unite)
                <option @if(old('lignes.'.$position.'.i_unite') == $o_unite->id) selected
                        @endif value="{{$o_unite->id}}">{{$o_unite->nom}}</option>
            @endforeach
        </select>
        @error('lignes.'.$position.'.i_unite')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.i_unite') }}
        </div>
        @enderror
    </td>
    <td>
        <input style="width: 120px" class="form-control prix_ht {{$errors->has('lignes.'.$position.'.i_prix_ht')? 'is-invalid' : ''}}"  type="number"
               name="lignes[{{$position}}][i_prix_ht]" value="{{old('lignes.'.$position.'.i_prix_ht')}}">
        @error('lignes.'.$position.'.i_prix_ht')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.i_prix_ht') }}
        </div>
        @enderror
    </td>
    <td>
        <input style="width: 120px" name="lignes[{{$position}}][i_reduction]"
               class="form-control reduction mb-1 {{$errors->has('lignes.'.$position.'.i_reduction')? 'is-invalid' : ''}}" type="number"
               value="{{old('lignes.'.$position.'.i_reduction')}}">
        @error('lignes.'.$position.'.i_reduction')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.i_reduction') }}
        </div>
        @enderror
        <select style="width: 120px" class="form-select row_select2 reduction_mode {{$errors->has('lignes.'.$position.'.i_reduction_mode')? 'is-invalid' : ''}}"
                name="lignes[{{$position}}][i_reduction_mode]" id="">
            <option
                @if(old('lignes.'.$position.'.i_reduction_mode') === 'pourcentage') selected
                @endif  value="pourcentage">%
            </option>
            <option @if(old('lignes.'.$position.'.i_reduction_mode') === 'fixe') selected
                    @endif  value="fixe">Fixe
            </option>
        </select>
        @error('lignes.'.$position.'.i_reduction_mode')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.i_reduction_mode') }}
        </div>
        @enderror
    </td>
    <td>
        <select style="width: 120px" name="lignes[{{$position}}][i_taxe]" id="" class="form-select row_select2 taxe {{$errors->has('lignes.'.$position.'.i_taxe')? 'is-invalid' : ''}}">
            @foreach($o_taxes as $o_taxe)
                <option
                    @if(old('lignes.'.$position.'.i_taxe') == $o_taxe->valeur) selected
                    @endif value="{{$o_taxe->valeur}}">{{$o_taxe->nom}}</option>
            @endforeach
        </select>
        @error('lignes.'.$position.'.i_taxe')
        <div class="invalid-feedback">
            {{ $errors->first('lignes.'.$position.'.i_taxe') }}
        </div>
        @enderror
    </td>
    <td>0.00 MAD</td>
    <td>
    </td>
</tr>
