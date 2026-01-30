@php
    $position= $o_ligne->position ?? ($key ?? -1);
@endphp
<tr>
    <td>
        <input type="hidden" class="article_id" name="lignes[{{$position}}][i_article_id]"
               value="{{$o_ligne['i_article_id']?? ($o_ligne['article_id'] ?? null)}}">
        <input type="hidden" class="article_reference" name="lignes[{{$position}}][i_article_reference]"
               value="{{$o_ligne['i_article_reference']?? ($o_ligne['article']['reference'] ?? null)}}">
        <div class="input-group mb-2">
            @if($o_ligne['i_article_id'] ?? ($o_ligne['article_id']?? false))
                <span
                    class="input-group-text">{{$o_ligne['i_article_reference']?? ($o_ligne['article']['reference'] ?? null)}}</span>
            @endif
            <input type="text" name="lignes[{{$position}}][i_article]"
                   class="form-control  {{$errors->has('lignes.'.$position.'.i_article')? 'is-invalid' : ''}}"
                   value="{{$o_ligne['i_article']?? ($o_ligne['nom_article'] ?? null)}}">
            <button type="button" class="btn btn-soft-primary article_btn"><i class="fa fa-store"></i></button>
        </div>
        @error('lignes.'.$position.'.i_article')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
        <div class="text-end">
            @if($o_ligne['i_description']?? ($o_ligne['description'] ?? null))
                <button class="btn btn-soft-primary btn-sm add-description " type="button"><i class="fa fa-plus"></i>
                    Modifier la description
                </button>
            @else
                <button class="btn btn-soft-success btn-sm add-description " type="button"><i class="fa fa-plus"></i>
                    Ajouter une description
                </button>
            @endif
        </div>
        <div class="description">
            {!!$o_ligne['i_description']?? ($o_ligne['description'] ?? null)!!}
        </div>
        <textarea name="lignes[{{$position}}][i_description]"
                  class="description-line d-none {{$errors->has('lignes.'.$position.'.i_description')? 'is-invalid' : ''}}
                  ">{{$o_ligne['i_description']?? ($o_ligne['description'] ?? null)}}</textarea>
        @error('lignes.'.$position.'.i_description')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </td>
    <td>
        <input style="width: 120px"
               class="form-control quantite mb-1 {{$errors->has('lignes.'.$position.'.i_quantite')? 'is-invalid' : ''}}"

               name="lignes[{{$position}}][i_quantite]" type="number"
               value="{{$o_ligne['i_quantite']?? ($o_ligne['quantite'] ?? null)}}">
        @error('lignes.'.$position.'.i_quantite')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
        <select class="form-select row_select2 {{$errors->has('lignes.'.$position.'.i_unite')? 'is-invalid' : ''}}"
                style="width: 120px" name="lignes[{{$position}}][i_unite]" id="">
            @foreach($o_unites as $o_unite)
                <option @if($o_ligne['i_unite']?? ($o_ligne['unit_id'] ?? null) === $o_unite->id) selected
                        @endif value="{{$o_unite->id}}">{{$o_unite->nom}}</option>
            @endforeach
        </select>
        @error('lignes.'.$position.'.i_unite')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </td>
    <td class=" @if(!$prix_revient) d-none @endif">
        <input style="width: 120px"
               class="form-control prix_revient {{$errors->has('lignes.'.$position.'.i_prix_revient')? 'is-invalid' : ''}}"
               type="number"
               name="lignes[{{$position}}][i_prix_revient]"
               value="{{number_format($o_ligne['i_prix_revient']?? ($o_ligne['revient'] ?? null),3,'.','')}}">
        @error('lignes.'.$position.'.i_prix_revient')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </td>
    <td>
        <input style="width: 120px"
               class="form-control prix_ht {{$errors->has('lignes.'.$position.'.i_prix_ht')? 'is-invalid' : ''}}"
               type="number"
               name="lignes[{{$position}}][i_prix_ht]"
               value="{{number_format($o_ligne['i_prix_ht']?? ($o_ligne['ht'] ?? null),3,'.','')}}">
        <button type="button" class="historique_prix_btn btn btn-soft-primary mt-2 w-100"><i class="fa fa-history"></i>
        </button>

        @error('lignes.'.$position.'.i_prix_ht')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </td>
    <td>
        <input style="width: 120px" name="lignes[{{$position}}][i_reduction]"
               class="form-control reduction mb-1 {{$errors->has('lignes.'.$position.'.i_reduction')? 'is-invalid' : ''}}"
               type="number"
               value="{{number_format($o_ligne['i_reduction']?? ($o_ligne['reduction'] ?? null),3,'.','')}}">
        @error('lignes.'.$position.'.i_reduction')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
        <select style="width: 120px"
                class="form-select row_select2 reduction_mode {{$errors->has('lignes.'.$position.'.i_reduction_mode')? 'is-invalid' : ''}}"
                name="lignes[{{$position}}][i_reduction_mode]" id="">
            <option
                @if($o_ligne['i_reduction_mode']?? ($o_ligne['mode_reduction'] ?? null) === 'pourcentage') selected
                @endif  value="pourcentage">%
            </option>
            <option @if($o_ligne['i_reduction_mode']?? ($o_ligne['mode_reduction'] ?? null) === 'fixe') selected
                    @endif  value="fixe">Fixe
            </option>
        </select>
        @error('lignes.'.$position.'.i_reduction_mode')
        <div class="invalid-feedback">
            {{$message}}
        </div>
        @enderror
    </td>
    <td>
        <select style="width: 120px" name="lignes[{{$position}}][i_taxe]" id=""
                class="form-select row_select2 taxe {{$errors->has('lignes.'.$position.'.i_taxe')? 'is-invalid' : ''}}">
            @foreach($o_taxes as $o_taxe)
                <option
                    @if($o_ligne['i_taxe']?? ($o_ligne['taxe'] ?? 0) == $o_taxe->valeur) selected
                    @endif value="{{$o_taxe->valeur}}">{{$o_taxe->nom}}</option>
            @endforeach
        </select>
        @error('lignes.'.$position.'.i_taxe')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </td>
    <td>{{$o_ligne['total_ttc'] ?? '0.00'.' MAD'}} </td>
    <td>
    </td>
</tr>
