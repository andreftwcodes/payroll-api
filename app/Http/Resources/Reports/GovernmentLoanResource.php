<?php

namespace App\Http\Resources\Reports;

use Carbon\Carbon;
use App\Libraries\Loan;
use App\Traits\PaySlipTrait;
use App\Models\GovernmentLoan;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernmentLoanResource extends JsonResource
{
    use PaySlipTrait;
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
        return $this->canNotDeduct() || $this->hasDeducted();
    }

    private function subject()
    {
        return strtoupper($this->subject);
    }

    private function message()
    {
        $message = '';

        $period  = $this->period();

        if ($this->canNotDeduct()) {
            $message = '- Deduction starts on ' . Carbon::parse($this->loaned_at)->addMonths(2)->format('F Y') . '.';
        } elseif ($this->hasDeducted()) {
            $message = '- Deducted for the month of ' . Carbon::parse($period['from'])->format('F Y') . '.';
        }

        return $message;
    }

    private function canNotDeduct()
    {
        return Loan::canNotDeduct($this->loaned_at);
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
