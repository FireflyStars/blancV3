<?php

use Illuminate\Support\Facades\Auth;
// SITE SUPPER MANAGER
define( 'DIGITAL_SUPPORT', 1 );

define( 'OWNER', 2 );
// MANAGERS
define( 'OFFICE_MANAGER', 3 );
define( 'OFFICE_SCHEDULER', 3 );
define( 'PRODUCTION_MANAGER', 4 );
define( 'SERVICE_MANAGER', 6 );
define( 'SALES_MANAGER', 10 );
// NORMAL USERS THAT HAS DIFFERENT ROLE
define( 'WAREHOUSE', 7 );
define( 'RECEIVING', 8 );
define( 'SALES_PEOPLE', 11 );
define( 'SALES_DESIGNER', 12 );
define( 'CREW', 13 );

if( !function_exists('check_user_in_role')) {
    function check_user_in_role($roles){
        return in_array(Auth::user()->user_role, $roles);
    }
}