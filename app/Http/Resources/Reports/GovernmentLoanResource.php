<?php

namespace App\Http\Resources\Reports;

use Carbon\Carbon;
use App\Libraries\Loan;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernmentLoanResource extends JsonResource
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
            'id'        => $this->id,
            'ref_no'    => $this->ref_no,
            'subject'   => $this->subject(),
            'message'   => $this->message()
        ];
    }

    private function subject()
    {
        return strtoupper($this->subject);
    }

    private function message()
    {
        $message = '';

        if (Loan::canNotDeduct($loaned_at = $this->loaned_at)) {
            $message = "- {$this->subject()} Loan deduction starts on " . Carbon::parse($loaned_at)->addMonths(2)->format('F Y') . '.';
        }

        return $message;
    }
}
