<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait CashAdvanceTrait
{
    public function childrenMapper($children)
    {
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
