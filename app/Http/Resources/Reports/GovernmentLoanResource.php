<?php

namespace App\Http\Resources\Reports;

use Carbon\Carbon;
use App\Libraries\Loan;
use App\Models\GovernmentLoan;
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
            'id'           => $this->id,
            'disabled'     => $this->disabled(),
            'amortization' => number_format($this->amortization, 2),
            'subject'      => $this->subject(),
            'message'      => $this->message()
        ];
    }

    private function disabled()
    {
        return $this->hasDeducted();
    }

    private function subject()
    {
        return strtoupper($this->subject);
    }

    private function message()
    {
        $message = '';
        $period  = $this->period();

        if (Loan::canNotDeduct($loaned_at = $this->loaned_at)) {
            $message = '- Deduction starts on ' . Carbon::parse($loaned_at)->addMonths(2)->format('F Y') . '.';
        } elseif ($this->hasDeducted()) {
            $message = '- Deducted for the month of ' . Carbon::parse($period['from'])->format('F Y') . '.';
        }

        return $message;
    }

    private function period()
    {
        return session('period');
    }

    private function hasDeducted()
    {
        $period = $this->period();

        return GovernmentLoan::find($this->id)
            ->government_loan_payments()
            ->whereMonth('paid_at', Carbon::parse($from = $period['from'])->format('m'))
            ->whereYear('paid_at', Carbon::parse($from)->format('Y'))
            ->exists();
    }
}
