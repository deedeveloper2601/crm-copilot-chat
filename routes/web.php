<?php

use App\Http\Controllers\CopilotController;
use Illuminate\Support\Facades\Route;

// Render the Vue Chat Interface
Route::get('/crm/copilot', [CopilotController::class, 'index'])->name('copilot.index');

// Handle the chat message and proxy it to Python
Route::post('/crm/copilot/ask', [CopilotController::class, 'ask'])->name('copilot.ask');