<?php

namespace App\Http\Resources\Reports;

use Illuminate\Support\Str;
use App\Traits\EmployeeTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Reports\TimeSheetChildrenResource;

class TimeSheetResource extends JsonResource
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
            'fullname' => $fullname = $this->getFullname(),
            'period'   => $period = 123,
            'attendances' => $attendances = TimeSheetChildrenResource::collection(
                $this->whenLoaded('attendances')
            ),
            'total_hours' => number_format($this->getTotalHours($attendances), 2, '.', ''),
            'print_url' => url('api/reports/timesheet/viewpdf', [$this->printParamsSecretKey()]),
            'filename' => Str::slug("timesheet-{$fullname}-{$period}", '-')
        ];
    }

    private function printParamsSecretKey()
    {
        return base64_encode(
            collect([
                'employee_id'   => $this->id,
                'from'          => $this->period[0],
                'to'            => $this->period[1]
            ])->toJson()
        );
    }

    private function getTotalHours($attendances)
    {
        return collect($attendances)->sum('hours');
    }

}
