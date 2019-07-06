<?php

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', 'Auth\RegisterController@action');
    Route::post('login', 'Auth\LoginController@action');
    Route::post('logout', 'Auth\LogoutController@logout');
    Route::get('me', 'Auth\MeController@action');
});

Route::post('employee/rate/{employee}', 'Employee\EmployeeRateController@store');
Route::post('employee/deductions/{employee}', 'Employee\EmployeeDeductionController@store');
Route::post('employee/schedules/{employee}', 'Employee\EmployeeScheduleController@store');
Route::post('employee/other/{employee}', 'Employee\EmployeeOtherController@store');
Route::post('employee/personal/validate', 'Employee\EmployeeValidatorController@personal');
Route::post('employee/employment/validate', 'Employee\EmployeeValidatorController@employment');
Route::patch('employee/attendance/attributes/{employee}', 'Attendance\UpdateAttendanceAttributesController@update');

Route::resource('employees', 'EmployeeController');
Route::resource('users', 'UserController');
Route::resource('roles', 'RoleController');
Route::resource('deductions', 'DeductionController');
Route::resource('locales', 'LocaleController');
Route::resource('attendances', 'Attendance\AttendanceController');
Route::resource('hdr-contributions', 'Contributions\HeaderContributionsController');
Route::resource('contribution-ranges', 'Contributions\ContributionRangesController');

Route::post('/testing', function (\Illuminate\Http\Request $request) { //test route

    $tc = (new \App\Libraries\TimeCalculator(
        $request->only(
            'sched_start_1',
            'sched_end_1',
            'sched_start_2',
            'sched_end_2',
            'timeIn',
            'timeOut'
        )
    ));
    // dd($tc->getWorkingHours());
    $request->merge([
        'working_hours' => $tc->getWorkingHours(),
        'hours_worked'  => $tc->getHours(),
        'shift'         => $tc->getShift()
    ]);
    // dd($request->only('rate', 'hours_worked', 'overtime', 'shift'));
    $calc = (new \App\Libraries\Calculator(
        $request->only('rate', 'working_hours', 'hours_worked', 'overtime', 'shift')
    ));

    dd($calc->getFormattedGrossPay());

});

Route::post('/validate-data-ranges', 'Contributions\ValidateDataRanges@action');

Route::get('/sidebar/menu/{user}', 'SystemTheme\SideBarMenuController@getMenu');

Route::get('/rate/history/{employee}', 'Rate\RateHistoryController@show');

Route::get('/reports/pay/employees', 'Reports\PayReportController@employees');
Route::get('/reports/pay/{employee}', 'Reports\PayReportController@pay');

Route::get('/reports/payslip/data', 'Reports\PaySlipController@getEmployees');
Route::get('/payslip/period/{employee}', 'Reports\PaySlipController@getPeriod');

Route::get('/payslip/pdf/{secret_key}', 'Reports\PaySlipController@viewToPDF');