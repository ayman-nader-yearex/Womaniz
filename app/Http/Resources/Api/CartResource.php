<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'price' => $this->price,
            'price_after_sale' => $this->price_after_sale,
            'quantity' => $this->quantity,
            'product' => new ProductResource($this->product),
        ];
        // $data = [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'desc' => $this->desc,
        //     'country_id' => $this->country_id,
        //     'thumbnail' => $this->thumbnail?  url('storage/'.$this->thumbnail) : null,
        //     'is_favourite' => $this->favoritedbyusers->contains(auth()->user()),
        //     'price' => $this->price,
        //     'price_after_sale' => $this->price_after_sale,
        //     'discount' => $this->discount,
        //     'product_type' => $this->product_type,
        //     'variants' => VariantResource::collection($this->variants),
        //     'images' => ImageResource::collection($this->images),
        // ];
        // if( $this->product_type == 'clothes' ){
        //     $data['ship_information_desc'] = $this->ship_information_desc; //clothes
        //     $data['fit_size_desc'] = $this->fit_size_desc; //clothes
        //     $data['return_order_desc'] = $this->return_order_desc; //clothes
        // }
        return $data;
    }
}
