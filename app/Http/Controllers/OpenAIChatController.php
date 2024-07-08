<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;
use Illuminate\Http\Request;

class OpenAIChatController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function send(Request $request)
    {
        $message = $request->input('message');
        $response = $this->openAIService->askChatGPT($message);

        return response()->json($response);
    }
}
