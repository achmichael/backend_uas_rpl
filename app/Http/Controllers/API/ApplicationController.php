<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        try {
            $request->validate([
                'post_id'    => 'required|uuid|exists:posts,id',
                'apply_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
                'amount'     => 'required|numeric',
            ]);

            $existingApplication = Application::where('post_id', $request->post_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if ($existingApplication) {
                return error('Application already exists for this post and applicant', 409);
            }

            $applicantId = JWTAuth::parseToken()->authenticate()->id;
            $request->merge(['applicant_id' => $applicantId]);

            // validate that the applicant exists in the database
            if (! \App\Models\User::find($applicantId)) {
                return error('Applicant does not exist', 404);
            }

            // validate that the applicant is not the post owner
            $post = \App\Models\Post::find($request->post_id);
            if ($post->posted_by === $applicantId) {
                return error('Applicant cannot be the post owner', 403);
            }

            $request->merge(['status' => 'pending']);

            $path = $request->file('apply_file')->store('applications');

            $request->merge(['apply_file' => $path]);
            
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
        } catch (ValidationException $e) {
            return error($e->errors(), 422);
        }
    }

    public function delete($id)
    {
        $application = Application::find($id);
        if (! $application) {
            return error('Application not found', 404);
        }

        $application->delete();
        return success($id, 'Application deleted successfully');
    }

    public function update($id)
    {
        $application = Application::find($id);
        if (! $application) {
            return error('Application not found', 404);
        }

        $application->update(request()->all());
        return success($application, 'Application updated successfully');
    }

    public function show($id)
    {
        $application = Application::find($id);
        if (! $application) {
            return error('Application not found', 404);
        }

        return success($application, 'Application retrieved successfully');
    }
}
