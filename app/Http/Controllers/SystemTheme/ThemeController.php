<?php

namespace App\Http\Controllers\SystemTheme;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ThemeController extends Controller
{
    public function getThemeData()
    {
        return [
            'data' => [
                'title' => 'Payroll Management System',
                'background' => asset('background/background.jpg')
            ]
        ];
    }
}
