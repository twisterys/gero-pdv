<?php

namespace App\Http\Resources\Api\caisse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'reference'=>$this->reference,
            'total'=> $this->montant ,
            'nom' => $this->nom_depense,
            'category'=> $this->categorie->nom,
            'date'=> Carbon::make($this->created_at)->format('H:i:s'),
            'statut'=>$this->statut_paiement,
            'beneficiaire'=>$this->pour
        ];
    }
}
