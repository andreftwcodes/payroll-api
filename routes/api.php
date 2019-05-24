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
    $grossPay = (new \App\Libraries\Calculator($request))->getGrossPay();
    dd($grossPay);
});

Route::get('/reports/pay/employees', 'Reports\PayReportController@employees');
Route::get('/reports/pay/{employee}', 'Reports\PayReportController@pay');
