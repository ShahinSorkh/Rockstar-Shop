<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'product' => $this->product->name,
            'price' => $this->price,
            'customizations' => $this->customizations->map(fn ($cus) => [
                'name' => $cus->product_customization->name,
                'option' => $cus->option,
            ]),
        ];
    }
}
