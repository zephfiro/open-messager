<?php

namespace App\Console\Commands;

use App\Models\OutboxMessage;
use Illuminate\Console\Command;

class ProcessOutboxMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outbox:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process outbox messages and mark them as sent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing outbox messages...');

        $messages = OutboxMessage::where('status', OutboxMessage::STATUS_PENDING)->get();

        foreach($messages as $message) {
            logger()->info('Processing message', [
                'event_id' => $message->event_id,
                'topic' => $message->topic,
                'payload' => $message->payload,
            ]);

            $message->status = OutboxMessage::STATUS_PROCESSED;
            $message->sent_at = now();
            $message->save();
        }

        $this->info('Outbox messages processed successfully.');
    }
}
