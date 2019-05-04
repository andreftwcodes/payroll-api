<?php

namespace App\Http\Controllers;

use App\Models\Locale;
use Illuminate\Http\Request;
use App\Http\Resources\Locale\LocaleResource;

class LocaleController extends Controller
{
    public function index()
    {
        return LocaleResource::collection(
            Locale::all()
        );
    }
}
