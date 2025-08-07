<?php

namespace App\Http\Resources\Api\parfums;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\DemandeTransfert
 */
class DemandeTransfertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lignes = [];
        foreach ($this->lignes as $ligne){
            $lignes[]=[
              'id'=>$ligne->id ,
              'article'=>$ligne->article->designation,
                'quantite_demande'=>$ligne->quantite_demande,
                'quantite_livre'=>$ligne->quantite_livre,
                'article_reference'=>$ligne->article->reference
            ];
        }
        return [
            'id'=>$this->id,
            'reference'=>$this->reference,
            'lignes'=>$lignes,
            'statut'=>$this->statut,
            'magasin_entree'=>$this->magasin_entree->reference,
            'magasin_sortie'=>$this->magasin_sortie->reference,
        ];
    }
}
