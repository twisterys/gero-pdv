<?php

namespace App\Http\Resources\Api\commercial;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MethodeLivraisonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $path = 'public/methodes-livraison/' . $this->image;
        return  [
           'id' => $this->id,
           'name' => $this->nom,
           'image' => $this->image ? 'data:image/*;base64,' . base64_encode(Storage::disk('external_storage')->get($path)) : null,
       ];
    }
}
