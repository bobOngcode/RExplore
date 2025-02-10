<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\EmployeeMasterData;
use App\EmployeeExplanation;
use Validator;
use Carbon\Carbon;
use DB;

class EmployeeNTEController extends Controller
{
    public function store(Request $request)
    {       
        try { 
            $employee_id = $request->employee_id;

            $valid_fields = [
                'employee_id' => 'required|integer',
                'date_issued' => 'required|date_format:Y-m-d',
                'issued_by' => 'required',
                'nte_code' => 'required',
                'violation' => 'required',
                'nte_file' => 'required',
            ];

            $rules = [
                'employee_id.required' => 'Employee ID is required',
                'date_issued.date_format' => 'Date Assigned is required',
                'date_issued.date_format' => 'Invalid date. Format: (YYYY-MM-DD)',
                'issued_by.required' => 'Issued By is required',
                'nte_code.required' => 'NTE Code is required',
                'nte_file.required' => 'NTE File is required',
                'violation.required' => 'Violation is required',
                
            ];

            $validator = Validator::make($request->all(), $valid_fields, $rules);

            if($validator->fails())
            {
                return response()->json($validator->errors());
            }

            $nte_file = '';
            $nte_file_extension ='';
            $explanation_file = '';
            $explanation_file_extension = '';

            if($request->hasFile('nte_file'))
            {
                $nte_file = $request->nte_file;
                $nte_file_extension = $nte_file->getClientOriginalExtension();

                $file_validator = Validator::make(
                    [   
                        'nte_file_ext' => strtolower($nte_file_extension),
                        'nte_file' => $nte_file,
                    ],
                    [
                        'nte_file_ext' => 'in:jpeg,jpg,png,docs,docx,pdf',
                        'nte_file_ext' => 'max: 5200',
                    ], 
                    [
                        'nte_file_ext.in' => 'NTE File type must be jpeg, jpg, png, docs, docx or pdf.',
                        'nte_file.max' => 'NTE File size maximum is 5MB.',
                    ]
                );  

                if($file_validator->fails())
                {
                    return response()->json($validator->errors());
                }
            }

            if($request->hasFile('explanation_file'))
            {
                $explanation_file = $request->explanation_file;
                $explanation_file_extension = $explanation_file->getClientOriginalExtension();

                $file_validator = Validator::make(
                    [   
                        'explanation_file_ext' => strtolower($explanation_file_extension),
                        'explanation_file' => $explanation_file,
                    ],
                    [
                        'explanation_file_ext' => 'nullable|in:jpeg,jpg,png,docs,docx,pdf',
                        'explanation_file_ext' => 'max: 5200'
                    ], 
                    [
                        'explanation_file_ext.in' => 'Explnation File type must be jpeg, jpg, png, docs, docx or pdf.',
                        'explanation_file.max' => 'Explnation File size maximum is 5MB.'
                    ]
                );  

                if($file_validator->fails())
                {
                    return response()->json($validator->errors());
                }
            }

            
            $file_date = Carbon::now()->format('Y-m-d');
            $nte_file_name = time().$nte_file->getClientOriginalName();
            $explanation_file_name = $explanation_file ? time().$explanation_file->getClientOriginalName() : '';
            $file_path = '/wysiwyg/employee_disciplinary_measure_files/' . $file_date;

            $nte_file->move(public_path() . $file_path, $nte_file_name);

            if($request->hasFile('explanation_file'))
            {
                $explanation_file->move(public_path() . $file_path, $explanation_file_name);
            }
            
            $explanation = new EmployeeExplanation();
            $explanation->employee_id = $employee_id;
            $explanation->date_issued =  $request->date_issued;
            $explanation->issued_by = $request->issued_by;
            $explanation->nte_code = $request->nte_code;
            $explanation->nte_file_name = $nte_file_name;
            $explanation->nte_file_path = $file_path;
            $explanation->nte_file_type = $nte_file_extension;
            $explanation->nte_date_upload = $file_date;
            $explanation->explanation_file_name = $explanation_file_name;
            $explanation->explanation_file_path = $explanation_file ? $file_path : '';
            $explanation->explanation_file_type = $explanation_file_extension;
            $explanation->explanation_date_upload = $explanation_file ? $file_date : '';
            $explanation->violation = $request->violation;
            $explanation->explanation_date = $request->explanation_date;
            $explanation->remarks = $request->remarks;
            $explanation->status = $request->status;
            $explanation->save();

            $explanations = EmployeeExplanation::select(DB::raw("*, date_format(created_at,'%Y-%m-%d') as create_date"))
                                                ->where('employee_id', $employee_id)
                                                ->orderBy('date_issued')
                                                ->get();
                                                
        
            return response()->json(['success' => 'Record has been added', 'explanations' => $explanations], 200);
        } catch (\Exception $e) {
            
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }


    public function update(Request $request, $id)
    {       
        
        $employee_id = $request->employee_id;

        try { 
            $employee_id = $request->employee_id;

            $valid_fields = [
                'employee_id' => 'required|integer',
                'date_issued' => 'required|date_format:Y-m-d',
                'issued_by' => 'required',
                'nte_code' => 'required',
                'violation' => 'required',
                // 'nte_file' => 'required',
            ];

            $rules = [
                'employee_id.required' => 'Employee ID is required',
                'date_issued.date_format' => 'Date Assigned is required',
                'date_issued.date_format' => 'Invalid date. Format: (YYYY-MM-DD)',
                'issued_by.required' => 'Issued By is required',
                'nte_code.required' => 'NTE Code is required',
                // 'nte_file.required' => 'NTE File is required',
                'violation.required' => 'Violation is required',
                
            ];

            $validator = Validator::make($request->all(), $valid_fields, $rules);

            if($validator->fails())
            {
                return response()->json($validator->errors());
            }

            $nte_file = '';
            $nte_file_extension ='';
            $explanation_file = '';
            $explanation_file_extension = '';

            if($request->hasFile('nte_file'))
            {
                $nte_file = $request->nte_file;
                $nte_file_extension = $nte_file->getClientOriginalExtension();

                $file_validator = Validator::make(
                    [   
                        'nte_file_ext' => strtolower($nte_file_extension),
                        'nte_file' => $nte_file,
                    ],
                    [
                        'nte_file_ext' => 'in:jpeg,jpg,png,docs,docx,pdf',
                        'nte_file_ext' => 'max: 5200',
                    ], 
                    [
                        'nte_file_ext.in' => 'NTE File type must be jpeg, jpg, png, docs, docx or pdf.',
                        'nte_file.max' => 'NTE File size maximum is 5MB.',
                    ]
                );  

                if($file_validator->fails())
                {
                    return response()->json($validator->errors());
                }
            }

            if($request->hasFile('explanation_file'))
            {
                $explanation_file = $request->explanation_file;
                $explanation_file_extension = $explanation_file->getClientOriginalExtension();

                $file_validator = Validator::make(
                    [   
                        'explanation_file_ext' => strtolower($explanation_file_extension),
                        'explanation_file' => $explanation_file,
                    ],
                    [
                        'explanation_file_ext' => 'nullable|in:jpeg,jpg,png,docs,docx,pdf',
                        'explanation_file_ext' => 'max: 5200'
                    ], 
                    [
                        'explanation_file_ext.in' => 'Explnation File type must be jpeg, jpg, png, docs, docx or pdf.',
                        'explanation_file.max' => 'Explnation File size maximum is 5MB.'
                    ]
                );  

                if($file_validator->fails())
                {
                    return response()->json($validator->errors());
                }
            }

            
            $file_date = Carbon::now()->format('Y-m-d');
            $nte_file_name = $nte_file ? time().$nte_file->getClientOriginalName() : '';
            $explanation_file_name = $explanation_file ? time().$explanation_file->getClientOriginalName() : '';
            $file_path = '/wysiwyg/employee_disciplinary_measure_files/' . $file_date;

            if($request->hasFile('nte_file'))
            {
                $nte_file->move(public_path() . $file_path, $nte_file_name);
            }

            if($request->hasFile('explanation_file'))
            {
                $explanation_file->move(public_path() . $file_path, $explanation_file_name);
            }
            
            $explanation = EmployeeExplanation::find($id);
            $explanation->employee_id = $employee_id;
            $explanation->date_issued =  $request->date_issued;
            $explanation->issued_by = $request->issued_by;
            $explanation->nte_code = $request->nte_code;

            // if nte file currently empty
            if(!$explanation->nte_file_name)
            {
                $explanation->nte_file_name = $nte_file_name;
                $explanation->nte_file_path = $nte_file ? $file_path : '';
                $explanation->nte_file_type = $nte_file_extension;
                $explanation->nte_date_upload = $file_date;
            }

            // if explanation file currently empty
            if(!$explanation->explanation_file_name)
            {
                $explanation->explanation_file_name = $explanation_file_name;
                $explanation->explanation_file_path = $explanation_file ? $file_path : '';
                $explanation->explanation_file_type = $explanation_file_extension;
                $explanation->explanation_date_upload = $file_date;
            }

            $explanation->violation = $request->violation;
            $explanation->explanation_date = $request->explanation_date;
            $explanation->remarks = $request->remarks;
            $explanation->status = $request->status;
            $explanation->save();

            $explanations = EmployeeExplanation::select(DB::raw("*, date_format(created_at,'%Y-%m-%d') as create_date"))
                                                ->where('employee_id', $employee_id)
                                                ->orderBy('date_issued')
                                                ->get();
        
            return response()->json(['success' => 'Record has been updated', 'explanations' => $explanations], 200);
        } catch (\Exception $e) {
            
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }
    
    public function delete(Request $request)
    {
        $explanation_id = $request->explanation_id;
        $explanation = EmployeeExplanation::findOrFail($explanation_id);
        
        $employee_id = $explanation->employee_id;

        $explanations = EmployeeExplanation::where('employee_id', $employee_id)->get();
        
        if($explanation->nte_file_name)
        {   
            $path = public_path() . '/wysiwyg/employee_disciplinary_measure_files';
            if(\File::isDirectory($path))
            {   
                $path = public_path() . $explanation->nte_file_path . "/" . $explanation->nte_file_name;
                unlink($path);
            }
            
        }

        if($explanation->explanation_file_name)
        {   
            $path = public_path() . '/wysiwyg/employee_disciplinary_measure_files';
            if(\File::isDirectory($path))
            {   
                $path = public_path() . $explanation->explanation_file_path . "/" . $explanation->explanation_file_name;
                unlink($path);
            }
            
        }

        $explanation->delete();

        $explanations = EmployeeExplanation::select(DB::raw("*, date_format(created_at,'%Y-%m-%d') as create_date"))
                                            ->where('employee_id', $employee_id)
                                            ->orderBy('date_issued')
                                            ->get();

        
        return response()->json(['success' => 'Record has been deleted', 'explanations' => $explanations], 200);
    }

    public function file_download(Request $request)
    {
        try {

            $file = EmployeeExplanation::find($request->explanation_id);

            $title = ''; 
            $file_path = '';    
            $file_name = '';
            $file_type = '';

            if($request->document_type == 'nte_file')
            {
                $file_path = $file->nte_file_path;    
                $file_name = $file->nte_file_name;
                $file_type = $file->nte_file_type;
            }
            elseif($request->document_type == 'explanation_file')
            {
                $file_path = $file->explanation_file_path;    
                $file_name = $file->explanation_file_name;
                $file_type = $file->explanation_file_type;
            }

            $file = public_path() . $file_path . "/" . $file_name;
            $headers = array('Content-Type: application/' . $file_type,);

            return response()->download($file, $file_name . '.' . $file_type, $headers);

        } catch (\Exception $e) {
                
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }

    public function file_delete(Request $request)
    {
        $explanation = EmployeeExplanation::find($request->explanation_id);

        try {
            if($request->document_type == 'nte_file')
            {
                $path = public_path() . '/wysiwyg/employee_disciplinary_measure_files';
                if(\File::isDirectory($path))
                {   
                    $path = public_path() . $explanation->nte_file_path . "/" . $explanation->nte_file_name;
                    unlink($path);
                }

                $explanation->update(['nte_file_name' => '', 'nte_file_path' => '', 'nte_file_type' => '']);
            }
            else if($request->document_type == 'explanation_file') 
            {
                $path = public_path() . '/wysiwyg/employee_disciplinary_measure_files';
                if(\File::isDirectory($path))
                {   
                    $path = public_path() . $explanation->explanation_file_path . "/" . $explanation->explanation_file_name;
                    unlink($path);
                }

                $explanation->update(['explanation_file_name' => '', 'explanation_file_path' => '', 'explanation_file_type' => '']);
            }

            $explanations = EmployeeExplanation::select(DB::raw("*, date_format(created_at,'%Y-%m-%d') as create_date"))
                                                ->where('employee_id', $explanation->employee_id)
                                                ->orderBy('date_issued')
                                                ->get();
        
            return response()->json(['success' => 'File has been deleted', 'explanations' => $explanations], 200);
        } catch (\Exception $e) {
                    
            return response()->json(['error' => $e->getMessage()], 200);
        }
        
        
    }
}
