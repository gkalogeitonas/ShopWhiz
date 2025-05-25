<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Agents\Tickets\TicketsAgent;
use NeuronAI\Chat\Messages\UserMessage;

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
        while (true) {
            $query = $this->ask('Enter your query (or type "exit" to quit)');
            if (trim(strtolower($query)) === 'exit') {
                $this->info('Goodbye!');
                break;
            }
            $agent = TicketsAgent::make();
            $response = $agent->chat(new UserMessage($query));
            $this->line('Agent Response: ' . $response->getContent());
        }
    }
}
