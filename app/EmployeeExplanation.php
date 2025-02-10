<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeExplanation extends Model
{
    protected $fillable = [
        'employee_id',
        'date_issued',
        'issued_by',
        'nte_code',
        'nte_file_name',
        'nte_file_path',
        'nte_file_type',
        'nte_date_upload',
        'explanation_file_name',
        'explanation_file_path',
        'explanation_file_type',
        'explanation_date',
        'explanation_date_upload',
        'violation',
        'remarks',
        'status',
    ];
}
