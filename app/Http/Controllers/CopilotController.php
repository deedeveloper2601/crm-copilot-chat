<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CopilotController extends Controller
{
    /**
     * Render the Vue.js Chat Interface
     */
    public function index()
    {
        // In a real application, you would pass the current lead dynamically.
        // For testing, we are hardcoding the email we seeded in Python.
        return Inertia::render('CRM/Copilot', [
            'leadEmail' => 'john@test.com' 
        ]);
    }

    /**
     * Proxy the request to the Python Microservice
     */
    public function ask(Request $request)
    {
        // 1. Validate the incoming frontend request
        $validated = $request->validate([
            'prompt' => 'required|string',
            'lead_email' => 'required|email',
        ]);

        try {
            // 2. Call the Python FastAPI server
            $response = Http::timeout(30)->post('http://127.0.0.1:8001/api/copilot', [
                'user_prompt' => $validated['prompt'],
                'lead_email' => $validated['lead_email'],
            ]);

            // 3. Return the response to Vue
            if ($response->successful()) {
                return response()->json([
                    'answer' => $response->json('ai_answer')
                ]);
            }

            return response()->json([
                'error' => 'Microservice returned an error.'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not connect to AI Copilot. Ensure main.py is running on port 8001.'
            ], 500);
        }
    }
}