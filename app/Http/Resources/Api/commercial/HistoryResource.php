<?php

namespace App\Http\Resources\Api\commercial;

use App\Models\Vente;
use App\Models\VenteLigne;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lignes = [];
        foreach ($this->lignes as $ligne){
            $lignes[]=[
              'article'=>$ligne->nom_article,
                'total_ttc'=>number_format($ligne->total_ttc,2,'.',' ').' MAD',
                'quantity' => $ligne->quantite,
                'unite' => $ligne->unite->nom,
                'reduction'=> $ligne->reduction.'%',
                'price'=> number_format($ligne->ht,2,'.',' ').' MAD',
                'article_reference'=>$ligne->article->reference

            ];
        }
        return [
            'id'=>$this->id,
            'reference'=>$this->reference,
            'type'=> __('ventes.'.$this->type_document),
            'total'=>number_format($this->total_ttc,2,'.',' ').' MAD',
            'lignes'=> $lignes,
            'client_nom'=> $this->client->nom,
            'time'=> Carbon::make($this->created_at)->format('d/m/Y H:i:s'),
            'commercial'=> $this->commercial?->nom,
            'statut'=>$this->statut,
            'total_ttc' => $this->total_ttc
        ];
    }
}
