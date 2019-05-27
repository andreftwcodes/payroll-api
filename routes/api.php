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

    $tc = (new \App\Libraries\TimeCalculator(
        $request->only(
            'sched_start_1',
            'sched_end_1',
            'sched_start_2',
            'sched_end_2',
            'timeIn',
            'timeOut',
            // 'special_person' => $this->special_person,
            'overtime'
        )
    ));
    // dd($tc);
    $request->merge([
        'hours_worked' => $tc->getHours(),
        'over_time'    => $tc->getOverTime(),
        'shift'        => $tc->getShift()
    ]);
    // dd($request->only('rate', 'hours_worked', 'over_time', 'shift'));
    $calc = (new \App\Libraries\Calculator(
        $request->only('rate', 'hours_worked', 'over_time', 'shift')
    ));

    dd($calc->getGrossPay());

});

Route::get('/reports/pay/employees', 'Reports\PayReportController@employees');
Route::get('/reports/pay/{employee}', 'Reports\PayReportController@pay');
