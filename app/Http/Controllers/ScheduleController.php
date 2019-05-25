<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Resources\Schedule\IndexScheduleResource;

class ScheduleController extends Controller
{
    public function index()
    {
        return IndexScheduleResource::collection(
            Schedule::all()
        );
    }
}
