<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AIController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        try {
            $message  = $request->input('message');
            $response = Gemini::geminiPro()->generateContent('Hello');
            return response()->json($response->text(), 200);
        } catch (\Exception $e) {
            Log::error('Gemini API error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get response from AI'], 500);
        }
    }

}
