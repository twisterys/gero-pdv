<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleUpdateRequest extends FormRequest
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
        return [
            'i_reference' => 'required|min:1|max:255|string|without_spaces|unique:articles,reference,'.$this->id,
            'i_designation' => 'required|min:1|max:255|string',
            'i_unite' => 'required|exists:unites,id',
            'i_famille' => 'nullable|exists:familles,id',
            'description' => 'nullable|string',
            'i_taxe' => 'required|exists:taxes,valeur',
            'i_vente_prix' => 'numeric|required',
            'i_achat_prix' => 'nullable|numeric',
            'i_revient_prix' => 'nullable|numeric',
            'i_image' => 'nullable|image',
            'i_quantite_alerte' => 'nullable|numeric',
            'i_marque_id'=>'nullable|exists:marques,id',
            'i_numero_serie'=>'nullable|string',
            'i_code_barre' => 'nullable|string',



        ];
    }

    public function attributes()
    {
        return [
            'i_reference' => 'référence',
            'i_designation' => 'désignation',
            'i_unite' => 'unité',
            'i_famille' => 'famille',
            'description' => 'description',
            'i_taxe' => 'taxe',
            'i_vente_prix' => 'prix de vente',
            'i_achat_prix' => "prix d'achat",
            'i_revient_prix' => "prix de revient",
            'i_image' => "prix de revient",
            'i_quantite_alerte' => "alerte quantité",
            'i_marque_id'=>'marque',
            'i_numero_serie'=>'numéro de série'
        ];
    }
}
