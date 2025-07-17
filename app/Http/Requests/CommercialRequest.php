<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommercialRequest extends FormRequest
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
        return [
            'nom' => 'required|string|min:3|max:100',
            'email' => 'nullable|email',
            'telephone' => ["nullable", "string", "regex:/^(((00|\+)212)|(0))(6|7|8|5)[0-9]{8}$/"],
            'note' => 'nullable|string|max:900',
            'reference' => ['required', 'max:255', Rule::unique('commercials', 'reference')->ignore($this->id)],
            'ice' => 'nullable',
            'commission_par_defaut' => 'nullable|numeric|max:100|min:0',
            'objectif' => 'nullable|numeric|between:0,9999999.99',
            'secteur' => 'nullable|string|max:255',
            "type_commercial" => "required|in:externe,interne",
            'i_image' => "nullable|image|extensions:jpeg,png,jpg|max:2048"
        ];
    }

    public function attributes()
    {
        $attributes = [
            'reference' => 'référence',
            'nom' =>  'Dénomination',
            'email' => 'email',
            'telephone' => 'téléphone',
            'note' => 'note',
            'type_commercial' => 'type',
            'commission_par_defaut' => 'commission par défaut',
            'objectif' => 'objectif',
            'secteur' => 'secteur',
            'i_image' => 'image'
        ];
        return $attributes;
    }
    public function messages()
    {
        return [
            'telephone.regex' => "Le format du numéro de téléphone est invalide, veuillez utiliser l'un de ces formats:\r
                                    - 0612345678
                                    - 0021212345678
                                    - +21212345678",

        ];
    }
}
