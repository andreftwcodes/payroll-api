<?php

namespace App\Http\Resources\CashAdvance;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CashAdvance\EmployeeShowResource;
use App\Http\Resources\CashAdvance\CashAdvanceChildrenResource;

class CashAdvanceShowResource extends JsonResource
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
            'employee' => new EmployeeShowResource(
                $this->whenLoaded('employee')
            ),
            'amount_deductible' => $this->amount_deductible,
            'children' => $this->children()
        ];
    }

    protected function children()
    {
        $children = CashAdvanceChildrenResource::collection(
            $this->whenLoaded('ca_children')
        );

        $balance = 0;
        $items  = [];

        foreach ($children as $key => $item) {

            $credit = !is_null($item['credit']) ? $item['credit'] : 0;
            $debit  = !is_null($item['debit']) ? $item['debit'] : 0;

            if ($key === 0) {

                $balance = $credit;

            } else {

                if ($credit) {

                    $balance = $balance + $credit;

                } elseif ($debit) {

                    $balance = $balance - $debit;
                    
                }
 
            }

            array_push($items, collect($item)->merge([
                'balance' => number_format($balance, 2)
            ]));

        }

        return $items;
    }
}
