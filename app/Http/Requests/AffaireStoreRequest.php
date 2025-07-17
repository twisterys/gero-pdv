<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AffaireStoreRequest extends FormRequest
{

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

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
            'i_reference' => 'required|unique:affaires,reference',
            'client_id' => 'required|exists:clients,id',
            'date_debut' => 'required|date_format:d/m/Y',
            'date_fin' => 'nullable|date_format:d/m/Y',
            'titre' => 'required|string|max:255',
            'cycle_duree'=> 'nullable|numeric|min:1',
            'budget_estimatif'=> 'nullable|numeric|min:0',
            'ca_global'=> 'nullable|numeric|min:0',
            'cycle_type' => 'nullable|string|in:jour,mois',
            'lignes' => 'nullable|array',
            'lignes.*.nom' => 'required|string|max:255',
            'lignes.*.jalon_date' => 'nullable|date_format:d/m/Y',
            'description' => 'nullable|string',
        ];

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [
            'i_reference' => "Référence",
            'client_id' => "Client",
            'date_debut' => "Date de début",
            'date_fin' => "Date de fin",
            'titre' => "Titre",
            'lignes' => 'Lignes de Jalon',
            'lignes.*.nom' => "Nom de jalon",
            'lignes.*.jalon_date' => 'Date de jalon',
            'cycle_duree'=> 'Durée de cycle',
            'budget_estimatif'=> 'Budget estimatif',
            'ca_global'=>  "CA global",
            'cycle_type' => 'Cycle type',
        ];
        return $attributes;
    }
}
