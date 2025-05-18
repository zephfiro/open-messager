<?php

namespace App\Console\Commands;

use App\Models\OutboxMessage;
use App\Services\KafkaProducerService;
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

    protected int $limit = 10000; // Limit for the number of messages to process in one go

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing outbox messages...');

        $kafka_producer = app(KafkaProducerService::class);

        while ($this->limit > 0) {
            $messages = OutboxMessage::where('status', OutboxMessage::STATUS_PENDING)
                ->orderBy('created_at')
                ->take(100)
                ->get();

            if ($messages->isEmpty()) break;

            foreach ($messages as $message) {
                $this->limit--;

                try {
                    $this->info("Attempting to send message with ID: {$message->event_id}");

                    $kafka_producer->produce(...$message->toProduce());

                    $message->status  = OutboxMessage::STATUS_PROCESSED;
                    $message->sent_at = now();
                    $message->save();

                    $this->info("Message with ID: {$message->event_id} sent successfully.");
                } catch (\Exception $e) {
                    $this->error("Failed to send message with ID: {$message->event_id}. Error: {$e->getMessage()}");
                    return 1;

                    $message->status = OutboxMessage::STATUS_FAILED;
                    $message->save();
                }
            }
        }

        $this->info('Outbox messages processed successfully.');
    }
}
