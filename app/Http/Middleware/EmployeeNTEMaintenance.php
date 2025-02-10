<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class EmployeeNTEMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        //Employee Master Data NTE Record
        if($request->is('api/employee_master_data/nte/index')){
            if($user->can('employee-master-data-nte-list')){
                return $next($request); 
            }
        }

        //Employee Master Data NTE Create
        if($request->is('api/employee_master_data/nte/create') || $request->is('api/employee_master_data/nte/store')){
            if($user->can('employee-master-data-nte-create')){
                return $next($request); 
            }
        }

        //Employee Master Data NTE Edit
        if($request->is('api/employee_master_data/nte/edit/*') || $request->is('api/employee_master_data/nte/update/*')){
            if($user->can('employee-master-data-nte-edit')){
                return $next($request); 
            }
        }

        //Employee Master Data NTE Delete
        if($request->is('api/employee_master_data/nte/delete')){
            if($user->can('employee-master-data-nte-delete')){
                return $next($request); 
            }
        }

        //Employee Master Data NTE File Download
        if($request->is('api/employee_master_data/nte/file_download')){
            if($user->can('employee-master-data-nte-file-download')){
                return $next($request); 
            }
        }

        //Employee Master Data NTE File Delete
        if($request->is('api/employee_master_data/nte/file_delete')){
            if($user->can('employee-master-data-nte-file-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
