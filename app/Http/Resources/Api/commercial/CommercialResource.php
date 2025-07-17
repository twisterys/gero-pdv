<?php

namespace App\Http\Resources\Api\commercial;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

class CommercialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $path = 'public/commerciaux/' . $this->image;
        return [
            'id' => $this->id,
            'name' => $this->nom,
            'type' => $this->type_commercial,
            'reference' => $this->reference,
            'image' => $this->image ? 'data:image/*;base64,' . base64_encode(Storage::disk('external_storage')->get($path)) : null,
        ];
    }
}
