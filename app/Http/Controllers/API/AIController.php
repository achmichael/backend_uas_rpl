<?php
namespace App\Http\Controllers\API;

use Gemini;
use Illuminate\Http\Request;
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
            $model = 'gemini-2.0-flash'; 
            $client = Gemini::client(config('gemini.api_key')); 
            $result = $client->generativeModel($model)->generateContent($message); 
            return response()->json($result->candidates[0]->content->parts[0]->text, 200);
        } catch (\Exception $e) {
            Log::error('Gemini API error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get response from AI'], 500);
        }
    }
}
