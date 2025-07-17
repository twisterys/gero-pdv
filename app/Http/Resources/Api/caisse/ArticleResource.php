<?php

namespace App\Http\Resources\Api\caisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->designation,
            'prix' => $this->prix_vente ,
            'tax'=>(1 + $this->taxe / 100),
            'stock'=>$this->magasin_stock(auth()->user()->activeMagasin()),
            'reference'=>$this->reference
        ];
    }
}
