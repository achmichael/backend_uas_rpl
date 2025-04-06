<?php

namespace App\Http\Controllers\API;

use App\Models\Job;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use function Laravel\Prompts\error;

class JobController extends Controller
{
    public function index(Request $request){
        if($request->has('q')){
            return $this->search($request);
        }
        DB::enableQueryLog();
        $jobs = Job::with(['post'])->get();
        Log::info('Query log', DB::getQueryLog());
        return response()->json([
            'succes'=>'succes',
            'data'=> $jobs,
        ]);
    }

    public function Jobs(Request $request)
    {
        try{
            $request->validate([
                'min_experience_year'    => 'required|numeric',
                'number_of_employee'     => 'required|numeric',
                'duration'               => 'required',
                'status'                 => 'required',
                'type_job'               => 'required',
                'type_salary'            => 'required',
                'system'                 => 'required',
            ]);

            $newRecord = Job::create($request->all());

            return response()->json([
                'status' => 'succes',
                'data' => $newRecord,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'massage' => $e->getMessage(),
                'errors' => $e->errors(),
            ],422);
        }
    }

    public function show($id){
        $job = Job::with(['post'])->find($id);
        if(! $job){
            return response()->json([
                'succes' => false,
                'massage'=> 'job apa sih yang kamu cari'
            ]);
        }
        return response()->json([
            'succes'=> true,
            'data'=> $job,
        ]);
    }

    public function search(Request $request){
        $Jobs = Job::with(['post'])
        ->where('title', 'like', '%'.$request->q.'%')
        ->orWhere('description','like', '%'.$request->q.'%' )
        ->get();

        return response()->json([
            'status'=> 'succes',
            'data'=> $Jobs,
        ]);
    }

    public function update(Request $request, $id)
    {
        try{
                $request->validate(
                [
                    'min_experience_year'    => 'required|numeric',
                    'number_of_employee'     => 'required|numeric',
                    'duration'               => 'required',
                    'status'                 => 'required',
                    'type_job'               => 'required',
                    'type_salary'            => 'required',
                    'system'                 => 'required',
                ]);
                $job = Job::find($id);

                if(! $job)
                {
                    return response()->json([
                        'massage' => 'Job not found',
                    ],404);
                }

                $job->update($request->all());
                return response()->json(
                [
                    'status' => 'succes',
                    'data' => $job
                ]);
            }catch(ValidationException $e)
                {
                 return response()->json(
                    [
                        'massage' => $e->getMessage(),
                        'errors' => $e->errors(),
                    ],422);
                }
    }

    public function delete($id)
    {

        $job = Job::find($id);

        if(! $job){
        return response()->json([
        'massage' => 'Job not found',
        ],404);
        }
        $job->delete();
        return response()->json([
                'status' => 'succes',
                'massage' => 'deleted succesfully'
        ]);

    }
}
