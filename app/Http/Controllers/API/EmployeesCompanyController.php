<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\EmployeesCompany;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class EmployeesCompanyController extends Controller
{
    public function index(Request $request)
    {
        $employees = EmployeesCompany::with(['employee' => function ($q) {
            $q->select('id', 'username', 'email', 'phone_number');
        }, 'company'])
            ->where('company_id', $request->company_id);
        return success($employees, 'Employees retrieved successfully');
    }

    public function show($id)
    {
        $employee = EmployeesCompany::with(['employee' => fn($q) => $q->select('id', 'username', 'email', 'phone_number'), 'company'])->find($id);
        if (! $employee) {
            return error('Employee not found', 404);
        }
        return success($employee, 'Employee retrieved successfully');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_id'  => 'required|uuid|exists:companies,id',
                'employee_id' => 'required|uuid|exists:users,id',
                'position'    => 'required|string|max:255',
                'status'      => 'required|in:active,inactive',
            ]);

            $employee = EmployeesCompany::create($request->all());
            return success($employee, 'Employee created successfully');
        } catch (ValidationException $e) {
            return error($e->errors(), 422);
        }
    }

    public function addByCompany(Request $request)
    {
        try {
            $request->validate([
                'username'    => 'required|string|min:3|max:50|unique:users,username',
                'email'       => 'required|email|max:50|unique:users,email',
                'password'    => 'required|string|min:8|max:200',
                'position'    => 'required|string|max:255',
                'status'      => 'required|in:active,inactive',
            ]);

            $data = $request->all();
            
            $data['company_id'] = JWTAuth::parseToken()->authenticate()->id;

            $user = User::create([
                'username' => $data['username'],
                'email'    => $data['email'],
                'password' => bcrypt($data['password']),
                'role_id'  => 5 // assumsing role_id 5 is for employees and clients
            ]);

            $data['employee_id'] = $user->id;

            $employee = EmployeesCompany::create($data);

            return success($employee, 'Employee added successfully');   
        }catch (ValidationException $e) {
            return error($e->errors(), 422);
        }
    }
}
