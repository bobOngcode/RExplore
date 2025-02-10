<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\EmployeeDisciplinary;
use Validator;
use Carbon\Carbon;
use DB;

class EmployeeDisciplinaryController extends Controller
{
    public function store(Request $request)
    {       
        try { 
            $employee_id = $request->employee_id;

            $valid_fields = [
                'employee_id' => 'required|integer',
                'date_issued' => 'required|date_format:Y-m-d',
                'nte_code' => 'required',
                'offense_code' => 'required',
                'offense' => 'required',
                'offense_type' => 'required',
                'disciplinary_action' => 'required',
                'file' => 'required',
                'series' => 'required',
                'status' => 'required',
            ];

            $rules = [
                'employee_id.required' => 'Employee ID is required',
                'date_issued.date_format' => 'Date Assigned is required',
                'date_issued.date_format' => 'Invalid date. Format: (YYYY-MM-DD)',
                'nte_code.required' => 'NTE Code is required',
                'offense_code.required' => 'Offense Code is required',
                'offense.required' => 'Offense is required',
                'offense_type.required' => 'Offense Type is required',
                'disciplinary_action.required' => 'Disciplinary Action is required',
                'file.required' => 'File is required',
                'series.required' => 'Series is required',
                'status.required' => 'Status is required',
                
            ];

            $validator = Validator::make($request->all(), $valid_fields, $rules);

            if($validator->fails())
            {
                return response()->json($validator->errors());
            }

            $file = '';
            $file_extension ='';

            if($request->hasFile('file'))
            {
                $file = $request->file;
                $file_extension = $file->getClientOriginalExtension();

                $file_validator = Validator::make(
                    [   
                        'file_ext' => strtolower($file_extension),
                        'file' => $file,
                    ],
                    [
                        'file_ext' => 'in:jpeg,jpg,png,docs,docx,pdf',
                        'file_ext' => 'max: 5200',
                    ], 
                    [
                        'file_ext.in' => 'NTE File type must be jpeg, jpg, png, docs, docx or pdf.',
                        'file.max' => 'NTE File size maximum is 5MB.',
                    ]
                );  

                if($file_validator->fails())
                {
                    return response()->json($validator->errors());
                }
            }
            
            $file_date = Carbon::now()->format('Y-m-d');
            $file_name = time().$file->getClientOriginalName();
            $file_path = '/wysiwyg/employee_disciplinary_measure_files/' . $file_date;

            $file->move(public_path() . $file_path, $file_name);
  
            $disciplinary = new EmployeeDisciplinary();
            $disciplinary->employee_id = $employee_id;
            $disciplinary->date_issued =  $request->date_issued;
            $disciplinary->nte_code = $request->nte_code;
            $disciplinary->offense_code = $request->offense_code;
            $disciplinary->offense = $request->offense;
            $disciplinary->offense_type = $request->offense_type;
            $disciplinary->disciplinary_action = $request->disciplinary_action;
            $disciplinary->file_name = $file_name;
            $disciplinary->file_path = $file_path;
            $disciplinary->file_type = $file_extension;
            $disciplinary->file_date_upload = $file_date;
            $disciplinary->series = $request->series;
            $disciplinary->transmit_date = $request->transmit_date;
            $disciplinary->return_date = $request->return_date;
            $disciplinary->status = $request->status;
            $disciplinary->save();

            $disciplinaries = EmployeeDisciplinary::select(DB::raw("*, date_format(created_at,'%Y-%m-%d') as create_date"))
                                                  ->where('employee_id', $employee_id)
                                                  ->orderBy('date_issued')
                                                  ->get();
        
            return response()->json(['success' => 'Record has been added', 'disciplinaries' => $disciplinaries], 200);
        } catch (\Exception $e) {
            
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }


    public function update(Request $request, $id)
    {       
        
        try { 
            $employee_id = $request->employee_id;

            $valid_fields = [
                'employee_id' => 'required|integer',
                'date_issued' => 'required|date_format:Y-m-d',
                'nte_code' => 'required',
                'offense_code' => 'required',
                'offense' => 'required',
                'offense_type' => 'required',
                'disciplinary_action' => 'required',
                // 'file' => 'required',
                'series' => 'required',
                'status' => 'required',
            ];

            $rules = [
                'employee_id.required' => 'Employee ID is required',
                'date_issued.date_format' => 'Date Assigned is required',
                'date_issued.date_format' => 'Invalid date. Format: (YYYY-MM-DD)',
                'nte_code.required' => 'NTE Code is required',
                'offense_code.required' => 'Offense Code is required',
                'offense.required' => 'Offense is required',
                'offense_type.required' => 'Offense Type is required',
                'disciplinary_action.required' => 'Disciplinary Action is required',
                // 'file.required' => 'File is required',
                'series.required' => 'Series is required',
                'status.required' => 'Status is required',
                
            ];

            $validator = Validator::make($request->all(), $valid_fields, $rules);

            if($validator->fails())
            {
                return response()->json($validator->errors());
            }

            $file = '';
            $file_extension ='';

            if($request->hasFile('file'))
            {
                $file = $request->file;
                $file_extension = $file->getClientOriginalExtension();

                $file_validator = Validator::make(
                    [   
                        'file_ext' => strtolower($file_extension),
                        'file' => $file,
                    ],
                    [
                        'file_ext' => 'in:jpeg,jpg,png,docs,docx,pdf',
                        'file_ext' => 'max: 5200',
                    ], 
                    [
                        'file_ext.in' => 'NTE File type must be jpeg, jpg, png, docs, docx or pdf.',
                        'file.max' => 'NTE File size maximum is 5MB.',
                    ]
                );  

                if($file_validator->fails())
                {
                    return response()->json($validator->errors());
                }
            }
            
            $file_date = Carbon::now()->format('Y-m-d');
            $file_name = $file ? time().$file->getClientOriginalName() : '';
            $file_path = '/wysiwyg/employee_disciplinary_measure_files/' . $file_date;

            if($request->hasFile('file'))
            {
                $file->move(public_path() . $file_path, $file_name);
            }
            
            $disciplinary = EmployeeDisciplinary::find($id);
            $disciplinary->employee_id = $employee_id;
            $disciplinary->date_issued =  $request->date_issued;
            $disciplinary->nte_code = $request->nte_code;
            $disciplinary->offense_code = $request->offense_code;
            $disciplinary->offense = $request->offense;
            $disciplinary->offense_type = $request->offense_type;
            $disciplinary->disciplinary_action = $request->disciplinary_action;
            
            // if file currently empty
            if(!$disciplinary->file_name)
            {
                $disciplinary->file_name = $file_name;
                $disciplinary->file_path = $file ? $file_path : '';
                $disciplinary->file_type = $file_extension;
                $disciplinary->file_date_upload = $file_date;
            }
            
            $disciplinary->series = $request->series;
            $disciplinary->transmit_date = $request->transmit_date;
            $disciplinary->return_date = $request->return_date;
            $disciplinary->status = $request->status;
            $disciplinary->save();

            $disciplinaries = EmployeeDisciplinary::select(DB::raw("*, date_format(created_at,'%Y-%m-%d') as create_date"))
                                                  ->where('employee_id', $employee_id)
                                                  ->orderBy('date_issued')
                                                  ->get();
        
            return response()->json(['success' => 'Record has been updated', 'disciplinaries' => $disciplinaries], 200);
        } catch (\Exception $e) {
            
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }
    
    public function delete(Request $request)
    {
        $disciplinary_id = $request->disciplinary_id;
        $disciplinary = EmployeeDisciplinary::findOrFail($disciplinary_id);
        
        $employee_id = $disciplinary->employee_id;

        $disciplinaries = EmployeeDisciplinary::where('employee_id', $employee_id)->get();
        
        if($disciplinary->file_name)
        {   
            $path = public_path() . '/wysiwyg/employee_disciplinary_measure_files';
            if(\File::isDirectory($path))
            {   
                $path = public_path() . $disciplinary->file_path . "/" . $disciplinary->file_name;
                unlink($path);
            }
            
        }

        $disciplinary->delete();

        $disciplinaries = EmployeeDisciplinary::select(DB::raw("*, date_format(created_at,'%Y-%m-%d') as create_date"))
                                            ->where('employee_id', $employee_id)
                                            ->orderBy('date_issued')
                                            ->get();

        return response()->json(['success' => 'Record has been deleted', 'disciplinaries' => $disciplinaries], 200);
    }

    public function file_download(Request $request)
    {
        try {

            $file = EmployeeDisciplinary::find($request->disciplinary_id);

            $file_path = $file->file_path;    
            $file_name = $file->file_name;
            $file_type = $file->file_type;
            
            $file = public_path() . $file_path . "/" . $file_name;
            $headers = array('Content-Type: application/' . $file_type,);

            return response()->download($file, $file_name . '.' . $file_type, $headers);

        } catch (\Exception $e) {
                
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }

    public function file_delete(Request $request)
    {
        $disciplinary = EmployeeDisciplinary::find($request->disciplinary_id);

        try {

            $path = public_path() . '/wysiwyg/employee_disciplinary_measure_files';
            if(\File::isDirectory($path))
            {   
                $path = public_path() . $disciplinary->file_path . "/" . $disciplinary->file_name;
                unlink($path);
            }

            $disciplinary->update(['file_name' => '', 'file_path' => '', 'file_type' => '']);
        
            $disciplinaries = EmployeeDisciplinary::select(DB::raw("*, date_format(created_at,'%Y-%m-%d') as create_date"))
                                                    ->where('employee_id', $disciplinary->employee_id)
                                                    ->orderBy('date_issued')
                                                    ->get();
        
            return response()->json(['success' => 'File has been deleted', 'disciplinaries' => $disciplinaries], 200);
        } catch (\Exception $e) {
                    
            return response()->json(['error' => $e->getMessage()], 200);
        }
        
        
    }
}
