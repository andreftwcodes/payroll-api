<?php

namespace App\Http\Resources\Reports;

use App\Models\Attendance;
use App\Traits\EmployeeTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Reports\GrossPayReportResource;

class GrossPayReportBaseResource extends JsonResource
{
    use EmployeeTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => GrossPayReportResource::collection($this->getData())
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'extra' => [
                'fullname' => $this->getFullname()
            ],
        ];
    }

    protected function getData()
    {
        return Attendance::with(['employee.rate', 'employee.schedule'])
                ->where('employee_id', $this->id)
                    ->orderBy('created_at', 'desc')
                        ->get();
    }
    
}
