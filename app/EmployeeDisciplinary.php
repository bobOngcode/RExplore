<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDisciplinary extends Model
{
    protected $fillable = [
        'employee_id',
        'date_issued',
        'nte_code',
        'offense_code',
        'offense',
        'offense_type',
        'disciplinary_action',
        'file_name',
        'file_path',
        'file_type',
        'file_date_upload',
        'series',
        'remarks',
        'transmit_date',
        'return_date',
        'status',
    ];
}
