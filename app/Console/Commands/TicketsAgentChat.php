<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Agents\Tickets\TicketsAgent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\History\InMemoryChatHistory;
use App\Logging\AgentLogger;

class TicketsAgentChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets-agent:chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Welcome to the Tickets Agent CLI!');
        $history = new InMemoryChatHistory();

        // Use the extracted AgentLogger class
        $logger = new AgentLogger();

        $agent = TicketsAgent::make()->observe($logger);

        while (true) {
            $query = $this->ask('Enter your query (or type "exit" to quit)');
            if (trim(strtolower($query)) === 'exit') {
                $this->info('Goodbye!');
                break;
            }
            $userMessage = new UserMessage($query);
            $history->addMessage($userMessage);
            $response = $agent->chat($history->getMessages());
            if ($response instanceof \NeuronAI\Chat\Messages\Message) {
                $history->addMessage($response);
                $this->line('Agent Response: ' . $response->getContent());
            } else {
                $this->line('Agent Response: ' . json_encode($response));
            }
        }
    }
}
