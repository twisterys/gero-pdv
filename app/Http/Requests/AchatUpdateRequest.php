<?php

namespace App\Http\Requests;

use App\Models\Achat;
use App\Services\GlobalService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AchatUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'reference' => 'required|max:255',
            'date_emission' => 'required|date_format:d/m/Y',
            'objet' => 'nullable|string|max:255',
            'i_note' => 'nullable|string',
            'lines' => 'array',
            'lignes.*.i_article' => 'required|string|max:255',
            'lignes.*.i_description' => 'nullable|max:60000',
            'lignes.*.i_prix_ht' => 'required|numeric|min:0|max:99999999.99',
            'lignes.*.i_quantite' => 'required|numeric|min:0.1|max:99999999.99',
            'lignes.*.i_unite' => 'required|exists:unites,id',
            'lignes.*.i_taxe' => 'required|exists:taxes,valeur',
            'lignes.*.i_reduction' => 'nullable|numeric|min:0|max:99999999.99',
            'lignes.*.i_reduction_mode' => 'required',
            'lignes.*.i_article_id' => 'nullable|exists:articles,id',
            'magasin_id' => 'required|exists:magasins,id',
        ];
        if ($this->type === 'bca') {
            $rules['reference'] = 'nullable';
        }
        if (in_array($this->type, ['dva', 'faa', 'fpa', 'bca'])) {
            $rules['date_expiration'] = 'required|date_format:d/m/Y';
        }
        if (GlobalService::get_modifier_reference() && Achat::find($this->id)->reference_interne ) {
            $rules['reference_interne'] = ['required', Rule::unique('achats', 'reference')->where(fn ($query) => $query->whereRaw('YEAR(date_emission) = ?', [$this->session()->get('exercice')]))->ignore($this->id)];
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'fournisseur_id' => "fournisseur",
            'date_emission' => __('achats.' . $this->type . '.date_emission'),
            'date_expiration' => __('achats.' . $this->type . '.date_expiration'),
            'total_ttc' => 'Total TTC',
            'total_ht' => 'Total HT',
            'objet' => "Objet",
            'commercial_id' => "Commercial",
            'commission_par_defaut' => "Commercial pourcentage",
            'lignes' => 'Lignes de achat',
            'lignes.*.i_article' => "Nom d'article",
            'lignes.*.i_description' => 'Description',
            'lignes.*.i_prix_ht' => 'Prix HT',
            'lignes.*.i_quantite' => "Quantité",
            'lignes.*.i_unite' => 'Unité',
            'lignes.*.i_taxe' => "TVA",
            'lignes.*.i_reduction' => "Réduction",
            'lignes.*.i_reduction_mode' => 'Mode de réduction',
            'i_note' => ' Note',
            'i_article_id' => 'article',
            'i_reference' => 'référence',
            'magasin_id' => 'magasin',
        ];
    }
}
