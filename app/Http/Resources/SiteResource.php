<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            "id" => (string)$this->id,
            "paper" => $this->paper,
            "cardboard" => $this->cardboard,
            "paper_cardboard_weight" => $this->paper_cardboard_weight,
            "paper_cardboard_price" => $this->paper_cardboard_price,
            "glass" => $this->glass,
            "plastic" => $this->plastic,
            "glass_plastic_weight" => $this->glass_plastic_weight,
            "glass_plastic_price" => $this->glass_plastic_price,
            "slider_delay" => $this->slider_delay,
            "created_at" => date('Y-m-d', strtotime($this->created_at)),
            "updated_at" => date('Y-m-d', strtotime($this->updated_at)),
        ];
    }
}
