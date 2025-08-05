<?php

namespace App\Http\Resources\Api\classic;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property string $designation
 * @property float $prix_vente
 * @property int $id
 * @property string $reference
 * @method magasin_stock(int $activeMagasin)
 * @mixin Article
 */
class ArticleCardResource extends JsonResource
{

    /**
     * @param Request $request
     * @return array
     *
     */
    #[ArrayShape(['id' => "mixed", 'designation' => "mixed", 'prix' => "mixed", 'quantity' => "mixed","reference"=>"string","unit"=>"string","image"=>"string","tax"=>"string"])] public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'designation'=>$this->designation,
            'reference'=> $this->reference,
            'prix'=>$this->prix_vente,
            'quantity'=>$this->magasin_stock($request->user()->activeMagasin()),
            'unit'=>$this->unite->nom,
            'tax'=>$this->taxe,
            'image'=>$this->image ? route('article.image.load', ['file' => $this->image]) : 'https://placehold.co/100x100?text=' . $this->reference,
        ];
    }
}
