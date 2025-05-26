<?php

namespace App\Support\Logging;

use SplObserver;
use SplSubject;

class AgentLogger implements SplObserver
{
    /**
     * Update method called by the subject when a change occurs
     *
     * @param SplSubject $subject The subject notifying the observer
     * @param string|null $event The event that occurred
     * @param mixed|null $data Additional data related to the event
     * @return void
     */
    public function update(SplSubject $subject, $event = null, $data = null): void
    {
        echo "[LOG] Event: {$event}" . PHP_EOL;
        if ($data) {
            echo "[LOG] Data: " . json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
        }
    }
}
