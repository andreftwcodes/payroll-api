<?php

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', 'Auth\RegisterController@action');
    Route::post('login', 'Auth\LoginController@action');
    Route::post('logout', 'Auth\LogoutController@logout');
    Route::get('me', 'Auth\MeController@action');
});

Route::post('employee/rate/{employee}', 'Employee\EmployeeRateController@store');
Route::post('employee/deductions/{employee}', 'Employee\EmployeeDeductionController@store');
Route::post('employee/other/{employee}', 'Employee\EmployeeOtherController@store');
Route::post('employee/personal/validate', 'Employee\EmployeeValidatorController@personal');
Route::post('employee/employment/validate', 'Employee\EmployeeValidatorController@employment');

Route::resource('employees', 'EmployeeController');
Route::resource('users', 'UserController');
Route::resource('roles', 'RoleController');
Route::resource('deductions', 'DeductionController');
Route::resource('schedules', 'ScheduleController');
Route::resource('locales', 'LocaleController');
Route::resource('attendances', 'Attendance\AttendanceController');

Route::post('/testing', function (\Illuminate\Http\Request $request) { //test route

    $deducCalc = new \App\Libraries\DeductionsCalculator(10800, [1]);

    dd($deducCalc->getEmployeeShareAmount());

    $tc = (new \App\Libraries\TimeCalculator(
        $request->only(
            'sched_start_1',
            'sched_end_1',
            'sched_start_2',
            'sched_end_2',
            'timeIn',
            'timeOut',
            // 'special_person' => $this->special_person
        )
    ));
    // dd($tc);
    $request->merge([
        'hours_worked' => $tc->getHours(),
        'shift'        => $tc->getShift()
    ]);
    // dd($request->only('rate', 'hours_worked', 'overtime', 'shift'));
    $calc = (new \App\Libraries\Calculator(
        $request->only('rate', 'hours_worked', 'overtime', 'shift')
    ));

    dd($calc->getGrossPay());

});

Route::get('/getpayslip', function (\Illuminate\Http\Request $request) {

    $attendance = \App\Models\Attendance::where('employee_id', 2)->whereBetween('created_at', [
        Carbon\Carbon::parse($request->from)->startOfDay()->toDateTimeString(),
        Carbon\Carbon::parse($request->to)->endOfDay()->toDateTimeString()
    ])->whereNotNull('start')->whereNotNull('end')->get()->toArray();

    $grossPay = 0;

    foreach ($attendance as $key => $value) {

        $tc = new \App\Libraries\TimeCalculator([
            'sched_start_1' => $value['sched_start_1'],
            'sched_end_1'   => $value['sched_end_1'],
            'sched_start_2' => $value['sched_start_2'],
            'sched_end_2'   => $value['sched_end_2'],
            'timeIn'        => $value['start'],
            'timeOut'       => $value['end'],
        ]);
    
        $calc = new \App\Libraries\Calculator([
            'rate'         => $value['amount'],
            'hours_worked' => $tc->getHours(),
            'overtime'     => $value['overtime'],
            'shift'        => $tc->getShift()
        ]);

        $grossPay += $calc->getGrossPay();
    }

    $deducCalc = new \App\Libraries\DeductionsCalculator($grossPay, [1]);

    $netPay = $grossPay - $deducCalc->getEmployeeShareAmount();
    
    dd(round($netPay, 2));
});

Route::get('/reports/pay/employees', 'Reports\PayReportController@employees');
Route::get('/reports/pay/{employee}', 'Reports\PayReportController@pay');
