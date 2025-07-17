<?php

namespace App\Http\Requests;

use App\Models\Fournisseur;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FournisseurStoreRequest extends FormRequest
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
            'reference' => 'required|unique:fournisseurs,reference',
            'forme_juridique' => ['required', Rule::exists('forme_juridique', 'id')],
            'nom' => 'required|string|min:3|max:100',
            'ice' => 'nullable|numeric|digits:15',
            'email' => 'nullable|email',
            'telephone' => ["nullable", "string", "regex:/^(((00|\+)212)|(0))(6|7|8|5)[0-9]{8}$/"],
            'note' => 'nullable|string|max:900',
            'adresse' => 'nullable|string|max:255',
            'limite_de_credit' => 'nullable|numeric|min:0|max:9999999.99',
            'rib'=>['nullable',"regex:/^[0-9]{3}\s[0-9]{3}\s[0-9]{16}\s[0-9]{2}$/"]
        ];
    }

    public function attributes()
    {
        $attributes = [
            'reference' => 'référence',
            'ice' => 'ICE',
            'nom' => in_array($this->get('forme_juridique'), ['sa', 'sarl']) ? 'Dénomination' : 'Dénomination',
            'forme_juridique' => 'forme juridique',
            'email' => 'email',
            'telephone' => 'téléphone',
            'note' => 'note',
            'limite_de_credit' => 'limite de credit',
            'adresse' => 'adresse',
        ];
        return $attributes;
    }

    public function messages()
    {
        return [
            'telephone.regex' => "Le format du numéro de téléphone est invalide, veuillez utiliser l'un de ces formats:\r
                                    - 0612345678
                                    - 0021212345678
                                    - +21212345678"
        ];
    }
}
