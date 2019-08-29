<?php

namespace App\Http\Controllers\Schedule;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Schedule\ScheduleIndexResource;

class ScheduleController extends Controller
{
    public function index()
    {
        return ScheduleIndexResource::collection(
            Schedule::all()
        );
    }
}
