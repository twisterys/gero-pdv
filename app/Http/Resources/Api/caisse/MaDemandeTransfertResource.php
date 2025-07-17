<?php

namespace App\Http\Resources\Api\caisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaDemandeTransfertResource extends JsonResource
{
    public function toArray(Request $request)
    {
        $lignes = [];
        foreach ($this->lignes as $ligne){
            $lignes[]=[
                'id'=>$ligne->id ,
                'article'=>$ligne->article->designation,
                'quantite_demande'=>+$ligne->quantite_demande,
                'quantite_livre'=>+$ligne->quantite_livre,
                'quantite_stock'=>$ligne->article->magasin_stock($this->magasin_entree->id)
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
