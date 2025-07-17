<?php

namespace App\Http\Requests;

use App\Models\Vente;
use App\Services\GlobalService;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VenteUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'client_id' => 'required|exists:clients,id',
            'date_emission' => 'required|date_format:d/m/Y',
            'objet' => 'nullable|string|max:255',
            'commercial_id' => 'nullable|exists:commercials,id',
            'i_commercial_pourcentage' => 'nullable|numeric|min:0|max:100',
            'lignes' => 'required|array',
            'lignes.*.i_article' => 'required|string|max:255',
            'lignes.*.i_description' => 'nullable|max:60000',
            'lignes.*.i_prix_ht' => 'required|numeric|min:0|max:99999999.99',
            'lignes.*.i_revient' => 'nullable|numeric|min:0|max:99999999.99',
            'lignes.*.i_quantite' => 'required|numeric|min:0.1|max:99999999.99',
            'lignes.*.i_unite' => 'required|exists:unites,id',
            'lignes.*.i_taxe' => 'required|exists:taxes,valeur',
            'lignes.*.i_reduction' => 'nullable|numeric|min:0|max:99999999.99',
            'lignes.*.i_reduction_mode' => 'required',
            'lignes.*.i_article_id' => 'nullable|exists:articles,id',
            'i_note' => 'nullable|string',
            'magasin_id' => 'nullable|exists:magasins,id'
        ];
        if (in_array($this->type, ['dv', 'fa', 'fp', 'bc'])) {
            $rules['date_expiration'] = 'required|date_format:d/m/Y';
        }
        if (GlobalService::get_modifier_reference() && Vente::find($this->id)->reference) {
            $rules['i_reference'] = ['required', Rule::unique('ventes', 'reference')->where(fn ($query) => $query->whereRaw('YEAR(date_emission) = ?', [$this->session()->get('exercice')]))->ignore($this->id)];
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'client_id' => "client",
            'date_emission' => __('ventes.' . $this->type . '.date_emission'),
            'date_expiration' => __('ventes.' . $this->type . '.date_expiration'),
            'objet' => "Objet",
            'commercial_id' => "Commercial",
            'i_commercial_pourcentage' => "Commercial pourcentage",
            'lignes' => 'Lignes de vente',
            'lignes.*.i_article' => "Nom d'article",
            'lignes.*.i_description' => 'Description',
            'lignes.*.i_prix_ht' => 'Prix HT',
            'lignes.*.i_quantite' => "Quantité",
            'lignes.*.i_unite' => 'Unité',
            'lignes.*.i_taxe' => "TVA",
            'lignes.*.i_reduction' => "Réduction",
            'lignes.*.i_reduction_mode' => 'Mode de réduction',
            'i_note' => 'Note',
            'i_article_id' => 'article',
            'i_reference' => 'référence',
            'magasin_id' => 'magasin'
        ];
    }
}
