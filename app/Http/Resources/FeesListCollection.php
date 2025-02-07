<?php

namespace App\Http\Resources;

use App\FeeInfo;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FeesListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($page){
            return [
                "id" => $page->id,
                "merchant_id" => $page->merchant_id,
                "merchant_name" => $page->merchant? $page->merchant->name: null,
                "sharing_levels" => $page->levels,
                "partners" => $page->partners,
                "fees_id" => $this->getFees($page->merchant_id),
                "created_at" => $page->created_at,
            ];
        });
    }

    public function getFees($merchantId)
    {
        $merchantFees = FeeInfo::with('feesCatalog')->where("merchant_id", $merchantId)->pluck('fees_id')->toArray();
        return $merchantFees;
    }
}
