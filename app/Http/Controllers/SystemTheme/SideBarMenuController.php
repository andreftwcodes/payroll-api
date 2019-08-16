<?php

namespace App\Http\Controllers\SystemTheme;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SideBarMenuController extends Controller
{

    public function getMenu(User $user)
    {
        $data = array(
            'data' => $this->selectedMenu($user)
        );

        return $data;
    }

    protected function selectedMenu($user)
    {
        if ($user->hasRole('admin')) {

            return $this->adminMenuData();

        } elseif ($user->hasRole('staff')) {

            return $this->staffMenuData();
            
        }
    }

    protected function adminMenuData()
    {
        return array(
            array(
                'icon' => 'dashboard',
                'title' => 'Dashboard',
                'to' => '/dashboard',
                'list_group' => false
            ),
            array(
                'icon' => 'group',
                'title' => 'Employees',
                'to' => '/employees',
                'list_group' => false
            ),
            array(
                'icon' => 'group_add',
                'title' => 'Attendance',
                'to' => '/attendance',
                'list_group' => false
            ),
            array(
                'icon' => 'money',
                'title' => 'Cash Advance',
                'to' => '/cash-advance',
                'list_group' => false
            ),
            array(
                'icon' => 'money',
                'title' => 'Loans',
                'to' => '',
                'list_group' => true,
                'childrens' => array(
                    array(
                        'icon' => 'people_outline',
                        'title' => 'SSS',
                        'to' => '/loans/sss',
                    )
                )
            ),
            array(
                'icon' => 'assignment',
                'title' => 'Reports',
                'to' => '',
                'list_group' => true,
                'childrens' => array(
                    // array(
                    //     'icon' => 'people_outline',
                    //     'title' => 'Daily Gross Pay',
                    //     'to' => '/reports/daily-gross-pay',
                    // ),
                    array(
                        'icon' => 'people_outline',
                        'title' => 'Payroll Periods',
                        'to' => '/reports/payroll-periods',
                    ),
                    array(
                        'icon' => 'people_outline',
                        'title' => 'Payslip',
                        'to' => '/reports/payslip',
                    ),
                )
            ),
            array(
                'icon' => 'settings',
                'title' => 'Settings',
                'to' => '',
                'list_group' => true,
                'childrens' => array(
                    array(
                        'icon' => 'people_outline',
                        'title' => 'Contributions',
                        'to' => '/settings/contributions',
                    ),
                    // array(
                    //     'icon' => 'people_outline',
                    //     'title' => 'Schedules',
                    //     'to' => '/settings/schedules',
                    // ),
                    // array(
                    //     'icon' => 'people_outline',
                    //     'title' => 'Locales',
                    //     'to' => '/settings/daily-gross-pay',
                    // )
                )
            ),
            array(
                'icon' => 'account_circle',
                'title' => 'Manage Users',
                'to' => '',
                'list_group' => true,
                'childrens' => array(
                    array(
                        'icon' => 'people_outline',
                        'title' => 'Users',
                        'to' => '/users',
                    ),
                    array(
                        'icon' => 'settings',
                        'title' => 'Roles',
                        'to' => '/roles',
                    )
                )
            ),
        );
    }

    protected function staffMenuData()
    {
        return array(
            array(
                'icon' => 'dashboard',
                'title' => 'Dashboard',
                'to' => '/dashboard',
                'list_group' => false
            ),
            array(
                'icon' => 'group_add',
                'title' => 'Attendance',
                'to' => '/attendance',
                'list_group' => false
            ),
        );
    }
}
