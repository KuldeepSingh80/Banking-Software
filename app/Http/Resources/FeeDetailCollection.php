<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FeeDetailCollection extends ResourceCollection
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
            $levels = $page->feeSharing;
            $payload = $page->payload;
            $data = [];
            foreach($payload as $p){
                if(isset($p[$page->name])){
                    $totalFees = $p[$page->name];
                    $respone = $page->charges_type === "fixed"?  $this->getFixedSplit($page->total_fee, $totalFees, $levels): $this->getPercentageSplit($totalFees, $levels);
                    array_push($data, [
                        "fee_name" => $page->name,
                        "levels" => $respone 
                    ]);
                }
            }
            return $data;
        });
    }

    public function getFixedSplit($fees, $actualfee, $levels)
    {
        $splitLevel = [];
        foreach($levels as $level){
            $splitPartner = [];
            foreach($level->partnerFeeSharing as $p){
                $diff = $actualfee/$fees;
                array_push($splitPartner,[
                    "name" => $p->partners->first_name,
                    "sharing_percentage" => $p->sharing,
                    "sharing_amount" => $p->fixed_cost * $diff,
                ]);
            }
            array_push($splitLevel, $splitPartner);
        }
        return $splitLevel;
    }

    public function getPercentageSplit($fees, $levels)
    {
        $splitLevel = [];
        dd($levels);
        foreach($levels as $level){
            $splitPartner = [];
            foreach($level->partnerFeeSharing as $p){
                array_push($splitPartner,[
                    "name" => $p->partners->first_name,
                    "sharing_percentage" => $p->sharing,
                    "sharing_amount" => $p->sharing,
                ]);
            }
            array_push($splitLevel, $splitPartner);
        }
        return $splitLevel;
    }
}
