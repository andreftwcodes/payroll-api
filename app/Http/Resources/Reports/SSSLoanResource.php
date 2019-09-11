<?php

namespace App\Http\Resources\Reports;

use Carbon\Carbon;
use App\Libraries\SSS_Loan;
use Illuminate\Http\Resources\Json\JsonResource;

class SSSLoanResource extends JsonResource
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
            'ref_no' => $this->ref_no,
            'message' => $this->message()
        ];
    }

    private function message()
    {
        $message = '';

        if (SSS_Loan::canNotDeduct($loaned_at = $this->loaned_at)) {
            $message = '- SSS Loan deduction starts on ' . Carbon::parse($loaned_at)->addMonths(3)->format('F Y') . '.';
        }

        return $message;
    }
}
