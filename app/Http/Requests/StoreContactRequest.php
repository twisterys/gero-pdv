<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'reference' => 'required|max:20',
            'ice' => 'nullable',
            'nom' => 'required',
            'is_client' => 'required_without_all:is_fournisseur,is_tier',
            'is_fournisseur' => 'required_without_all:is_client,is_tier',
            'is_tier' => 'required_without_all:is_client,is_fournisseur',
            'formJuridique' => 'required|in:sarl,sa,personne_physique,auto_entrepreneur',
            'email' => 'nullable|email',
            'telephone' => 'nullable',
            'note' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'reference.required' => 'Le champ de référence est requis.',
            'reference.max' => 'La référence ne peut pas dépasser :max caractères.',
            'ice.nullable' => 'Le champ ICE doit être nullable.',
            'nom.required' => 'Le champ du nom est requis.',
            'is_client.required_without_all' => 'Veuillez cocher au moins une option parmi Client, Fournisseur, ou Tier.',
            'is_fournisseur.required_without_all' => 'Veuillez cocher au moins une option parmi Client, Fournisseur, ou Tier.',
            'is_tier.required_without_all' => 'Veuillez cocher au moins une option parmi Client, Fournisseur, ou Tier.',
            'formJuridique.required' => 'Le champ de la forme juridique est requis.',
            'formJuridique.in' => 'La forme juridique doit être parmi :values.',
            'email.email' => 'Le champ de l\'e-mail doit être une adresse e-mail valide.',
            'telephone.nullable' => 'Le champ du téléphone peut être nullable.',
            'note.nullable' => 'Le champ de la note peut être nullable.',
        ];
    }
}
