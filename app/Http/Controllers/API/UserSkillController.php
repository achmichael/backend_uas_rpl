<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserSkills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserSkillController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'skill_id' => 'required|numeric|exists:skills,id', // to validate the skill_id                                         // 'skill_id.*' => 'integer|exists:skills,id' // to validate each element
            ]);

            $userSkill = UserSkills::create([
                'user_id'  => Auth::user()->id,
                'skill_id' => $request->skill_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Skill added successfully',
                'data'    => $userSkill,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function index()
    {
        $userSkills = UserSkills::with('skill')->where('user_id', Auth::user()->id)->get();

        return response()->json([
            'success' => true,
            'data'    => $userSkills,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'skill_id' => 'required|numeric|exists:skills,id', // to validate the skill_id
            ]);

            $userSkill = UserSkills::find($id);
            if (! $userSkill) {
                return response()->json([
                    'success' => false,
                    'message' => 'User skill not found',
                ], 404);
            }

            $userSkill->skill_id = $request->skill_id;

            $userSkill->save();
            return response()->json([
                'success' => true,
                'message' => 'Skill updated successfully',
                'data'    => $userSkill,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function destroy($id)
    {
        $userSkill = UserSkills::find($id);

        if (! $userSkill) {
            return response()->json([
                'success' => false,
                'message' => 'User skill not found',
            ], 404);
        }

        $userSkill->delete();

        return response()->json([
            'success' => true,
            'message' => 'User skill deleted successfully',
        ]);
    }
}
