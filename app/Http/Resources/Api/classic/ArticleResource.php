<?php

namespace App\Http\Resources\Api\classic;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Article
 */
class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->designation,
            'prix' => $this->prix_vente ,
            'stock'=>$this->magasin_stock(auth()->user()->activeMagasin()),
            'reference'=>$this->reference,
            'tax'=>$this->taxe
        ];
    }
}
