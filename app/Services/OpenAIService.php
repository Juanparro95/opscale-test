<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function askChatGPT($prompt)
    {
        try {
            $response = $this->client->post('completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo-0125',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 150,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return $body['choices'][0]['message']['content'] ?? 'No response';
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                if ($statusCode === 429) {
                    return "You have exceeded your current quota. Please check your plan and billing details.";
                }
                return "Error: " . $response->getBody()->getContents();
            }

            return "An error occurred: " . $e->getMessage();
        }
    }
}
