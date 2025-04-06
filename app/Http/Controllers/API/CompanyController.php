<?php

namespace App\Http\Controllers\API;

use App\Models\Company;

use Faker\Guesser\Name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CompanyController extends Controller
{

    public function index(Request $request){
        if($request->has('q')){
            return $this->search($request);
        }
        DB::enableQueryLog();
        $company = Company::with('user')->get();
        Log::info('Query log',DB::getQueryLog());
        return response()->json([
            'succes' => 'succes',
            'data' => $company
        ]);
    }

    public function create(Request $request){
        try{
            $request->validate([
                'name'      => 'required|string',
                'image'     => 'required|string',
                'addres'    => 'required|string',
                'phone'     => 'required|string',
                'email'     => 'required|email',
                'website'   => 'required|string',
                'founded'   => 'required|numeric',
            ]);
            $data = $request->all();
            $data['user_id'] = auth()->id();
            $company = Company::create($data);

            return response()->json([
                'status' => 'succes',
                'data' => $company,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'massage' => $e->getMessage(),
                'error'   => $e->errors(),
            ]);
        }
    }

    public function check($id){
        $company = Company::with('user')->find($id);
        if(! $company){
            return response()->json([
                'succes'  => false,
                'massage' => 'company is nothing beb',
            ]);
        }
        return response()->json([
            'succes ' => true,
            'data' => $company,
        ]);
    }

    public function search(Request $request){

        $company = Company::with(['user'])
        ->where('name', 'like', '%' . $request->q . '%')
        ->get();

        if($company->isEmpty()){
           return response()->json([
            'status' => 'error',
            'data' => 'company not found',
           ]);
        }

        return response()->json([
            'status'    => 'succes',
            'data'      => $company,
        ]);
    }

    public function update(Request $request, $id){
        try{
            $request->validate([
                'name'      => 'required|string',
                'image'     => 'required|string',
                'addres'    => 'required|string',
                'phone'     => 'required|numeric',
                'email'     => 'required|email',
                'website'   => 'required|string',
                'founded'   => 'required|numeric',
            ]);

            $company = Company::find($id);
            if(! $company)
            {
                return response()->json([
                    'massage'   => 'company apa sih yang lagi di cari',
                ],404);
            }

            $company->update($request->all());
            return response()->json([
                'status'  => 'succes',
                'data' => $company,
            ]);
        }catch(ValidationException $e){
            return response()->json([
                'massage' => $e->getMessage(),
                'error'  => $e->errors(),
            ],422);
        }
    }

    public function delete($id)
    {
        $company = Company::find($id);
        if(! $company){
            return response()->json([
                'massage' => 'company is nothing',
            ]);
        }
        $company->delete();
        return response()->json([
            'status' => 'succes',
            'data' => $company,
        ]);
    }
    public function show($id)
    {
        $company = Company::with('user','job.post')->findOrFail($id);
        if(! $company){
            return response()->json([
                'succes'    => false,
                'messege'   => 'company not found',
            ],404);
        }

        return response()->json([
            'succes'    => true,
            'data'  => $company,
        ]);

    }





















}

