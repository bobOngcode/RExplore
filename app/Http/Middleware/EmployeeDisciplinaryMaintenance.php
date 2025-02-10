<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class EmployeeDisciplinaryMaintenance
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

        //Employee Master Data Disciplinary Record
        if($request->is('api/employee_master_data/disciplinary/index')){
            if($user->can('employee-master-data-disciplinary-list')){
                return $next($request); 
            }
        }

        //Employee Master Data Disciplinary Create
        if($request->is('api/employee_master_data/disciplinary/create') || $request->is('api/employee_master_data/disciplinary/store')){
            if($user->can('employee-master-data-disciplinary-create')){
                return $next($request); 
            }
        }

        //Employee Master Data Disciplinary Edit
        if($request->is('api/employee_master_data/disciplinary/edit/*') || $request->is('api/employee_master_data/disciplinary/update/*')){
            if($user->can('employee-master-data-disciplinary-edit')){
                return $next($request); 
            }
        }

        //Employee Master Data Disciplinary Delete
        if($request->is('api/employee_master_data/disciplinary/delete')){
            if($user->can('employee-master-data-disciplinary-delete')){
                return $next($request); 
            }
        }

        //Employee Master Data Disciplinary File Download
        if($request->is('api/employee_master_data/disciplinary/file_download')){
            if($user->can('employee-master-data-disciplinary-file-download')){
                return $next($request); 
            }
        }

        //Employee Master Data Disciplinary File Delete
        if($request->is('api/employee_master_data/disciplinary/file_delete')){
            if($user->can('employee-master-data-disciplinary-file-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
