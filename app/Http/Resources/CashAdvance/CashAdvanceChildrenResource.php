<?php

namespace App\Http\Resources\CashAdvance;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CashAdvanceChildrenResource extends JsonResource
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
            'date' => $this->date,
            'transact_by' => $this->transactBy(),
            'date_dsp' => $this->formattedDate($this->date),
            'credit' => $this->credit,
            'credit_dsp' => $this->formatted($this->credit),
            'debit' => $this->debit,
            'debit_dsp' => $this->formatted($this->debit),
            'is_payroll' => $this->isPayroll()
        ];
    }

    private function formattedDate($date)
    {
        return Carbon::parse($date)->toFormattedDateString();
    }

    private function transactBy()
    {
        return $this->isPayroll() ? 'Payroll' : 'Manual';
    }

    private function isPayroll()
    {
        return !is_null($this->payslip_id);
    }

    protected function formatted($value)
    {
        return !is_null($value) ? number_format($value, 2) : null;
    }
}
