<?php

namespace App\Console\Commands;

use App\Http\Controllers\OpenAIChatController;
use App\Services\OpenAIService;
use Illuminate\Console\Command;

class AskOpenAICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ask:openai {question}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ask a question to OpenAI GPT';

    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        parent::__construct();
        $this->openAIService = $openAIService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $question = $this->argument('question');
        $response = $this->openAIService->askChatGPT($question);

        $this->info('Response:');
        $this->line($response);

        if (str_contains($response, 'getUsersCreatedInLast24Hours')) {
            $controller = new OpenAIChatController($this->openAIService);
            $users = $controller->getUsersCreatedInLast24Hours();
            $this->info('Users created in the last 24 hours:');
            $this->line($users->getContent());
        }
    }
}
