<tr>

    <td>
        <input type="hidden" class="form-control id"  name="lignes[-1][id]">
        <input class="form-control nom" type="text" name="lignes[-1][nom]">
    </td>
    <td>
        <input type="text"
               class="form-control jalon_date {{ $errors->has('jalon_date') ? 'is-invalid' : '' }}"
               name="lignes[-1][jalon_date]" readonly required
               value="{{ old('jalon_date', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">
    </td>
    <td></td>
</tr>
