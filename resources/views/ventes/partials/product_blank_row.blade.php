<tr>

    <td>
        <input type="hidden" class="article_id" name="lignes[-1][i_article_id]">
        <input type="hidden" class="article_reference" name="lignes[-1][i_article_reference]">
        <div class="input-group mb-2">
            <input type="text" name="lignes[-1][i_article]"
                   class="form-control">
            <button type="button" class="btn btn-soft-primary article_btn"><i class="fa fa-store"></i></button>
        </div>
        <div class="text-end">
            <button class="btn btn-soft-success btn-sm add-description " type="button"><i class="fa fa-plus"></i>
                Ajouter une description
            </button>
        </div>
        <div class="description"></div>
        <textarea name="lignes[-1][i_description]" class="description-line d-none"></textarea>
    </td>
    <td>
        <input style="width: 120px" class="form-control quantite mb-1 " name="lignes[-1][i_quantite]" type="number">
        <select class="form-select row_select2 unite " style="width: 120px" name="lignes[-1][i_unite]" id="">
            @foreach($o_unites as $o_unite)
                <option value="{{$o_unite->id}}">{{$o_unite->nom}}</option>
            @endforeach
        </select>
    </td>
    <td class=" @if(!$prix_revient) d-none @endif">
        <input style="width: 120px" class="form-control prix_revient " type="number" name="lignes[-1][i_prix_revient]">
    </td>
    <td>
        <input style="width: 120px" class="form-control prix_ht " type="number" name="lignes[-1][i_prix_ht]">
        <button type="button" class="historique_prix_btn btn btn-soft-primary mt-2 w-100"><i class="fa fa-history"></i>
        </button>
    </td>
    <td>
        <input style="width: 120px" name="lignes[-1][i_reduction]" class="form-control reduction mb-1 " type="number">
        <select style="width: 120px" class="form-select row_select2 reduction_mode " name="lignes[-1][i_reduction_mode]"
                id="">
            <option value="pourcentage">%</option>
            <option value="fixe">Fixe</option>
        </select>

    </td>
    <td>
        <select style="width: 120px" name="lignes[-1][i_taxe]" id="" class="form-select row_select2 taxe ">
            @foreach($o_taxes as $o_taxe)
                <option value="{{$o_taxe->valeur}}">{{$o_taxe->nom}}</option>
            @endforeach
        </select>
    </td>
    <td></td>
    <td>
    </td>
</tr>
