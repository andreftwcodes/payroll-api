<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Resources\Schedule\ScheduleResource;

class ScheduleController extends Controller
{
    public function index()
    {
        return ScheduleResource::collection(
            Schedule::all()
        );
    }
}
