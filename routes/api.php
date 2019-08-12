<?php

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', 'Auth\RegisterController@action');
    Route::post('login', 'Auth\LoginController@action');
    Route::post('logout', 'Auth\LogoutController@logout');
    Route::get('me', 'Auth\MeController@action');
});

Route::get('attendances/get-employees', 'Attendance\AttendanceController@getDropDownEmployees');
Route::get('employee/rate-histories/{employee}', 'Employee\EmployeeRateHistoryController@show');

Route::post('employee/schedules/{employee}', 'Employee\EmployeeScheduleController@store');
Route::post('employee/other/{employee}', 'Employee\EmployeeOtherController@store');
Route::post('employee/rate-history/{employee}', 'Employee\EmployeeRateHistoryController@store');
Route::post('employee/personal/validate', 'Employee\EmployeeValidatorController@personal');
Route::post('employee/employment/validate', 'Employee\EmployeeValidatorController@employment');
Route::patch('employee/status/{employee}', 'Employee\EmployeeStatusController@update');
Route::patch('employee/attendance/attributes/{employee}', 'Attendance\UpdateAttendanceAttributesController@update');

Route::resource('employees', 'EmployeeController');
Route::resource('users', 'UserController');
Route::resource('roles', 'RoleController');
Route::resource('locales', 'LocaleController');
Route::resource('attendances', 'Attendance\AttendanceController');
Route::resource('schedules', 'Schedule\ScheduleController');
Route::resource('hdr-contributions', 'Contributions\HeaderContributionsController');
Route::resource('contribution-ranges', 'Contributions\ContributionRangesController');

Route::post('/testing', function (\Illuminate\Http\Request $request) { //test route
    //
});

Route::group(['prefix' => 'cash-advance'], function () {
    Route::get('index', 'CashAdvance\CashAdvanceController@index');
    Route::get('show/{employee}', 'CashAdvance\CashAdvanceController@show');
    Route::post('store', 'CashAdvance\CashAdvanceController@store');
    Route::post('attach_ledger/{employee}', 'CashAdvance\CashAdvanceController@attachLedger');
    Route::patch('amount_deductible/{ca_parent}', 'CashAdvance\CashAdvanceController@amount_deductible');
});

Route::group(['prefix' => 'reports-validator'], function () {
    Route::post('deduction-filters', 'Reports\Validator@deductionFilters');
});

Route::post('/validate-data-ranges', 'Contributions\ValidateDataRanges@action');

Route::get('/sidebar/menu/{user}', 'SystemTheme\SideBarMenuController@getMenu');

Route::get('/reports/pay/employees', 'Reports\PayReportController@employees');
Route::get('/reports/pay/{employee}', 'Reports\PayReportController@pay');

Route::get('/reports/payslip/data', 'Reports\PaySlipController@getEmployees');
Route::get('/payslip/period/{employee}', 'Reports\PaySlipController@getPeriod');

Route::get('/payslip/pdf/{secret_key}', 'Reports\PaySlipController@viewToPDF');

