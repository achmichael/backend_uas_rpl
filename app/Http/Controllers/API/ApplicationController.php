<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        try {
            $request->validate([
                'post_id'      => 'required|uuid',
                'applicant_id' => 'required|uuid',
                'apply_file'   => 'required|string|max:300',
                'amount'       => 'required|numeric',
                'status'       => 'required|in:pending,accepted,rejected',
            ]);

            $existingApplication = Application::where('post_id', $request->post_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if ($existingApplication) {
                return error('Application already exists for this post and applicant', 409);
            }

            $application = Application::create($request->all());

            return success($application, 'Application created successfully');
        } catch (ValidationException $e) {
            return error($e->errors(), 422);
        }
    }

    public function changeState(Request $request, $id)
    {
        try {
            $request->validate([
                'approver_id' => 'required|uuid',
                'status'      => 'required|in:accepted,rejected',
            ]);
    
            $application = Application::find($id);
    
            if (! $application) {
                return error('Application not found', 404);
            }
    
            $application->changeStatus($request->status);
            return success($application, 'Application status updated successfully');
        }catch (ValidationException $e){
            return error($e->errors(), 422);
        }
    }
    
    public function delete($id)
    {
        $application = Application::find($id);
        if (!$application) return error('Application not found', 404);
        $application->delete();
        return success($id, 'Application deleted successfully');
    }

    public function update($id)
    {
        $application = Application::find($id);
        if (!$application) return error('Application not found', 404);
        $application->update(request()->all());
        return success($application, 'Application updated successfully');
    }

    public function show($id)
    {
        $application = Application::find($id);
        if (!$application) return error('Application not found', 404);
        return success($application, 'Application retrieved successfully');
    }
}
