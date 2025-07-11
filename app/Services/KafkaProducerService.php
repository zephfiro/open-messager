<?php

namespace App\Services;

use RdKafka\Producer;
use RdKafka\Conf;

class KafkaProducerService
{
    protected Producer $producer;
    protected array $config;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->config = config('kafka.producer');

        $conf = new Conf();
        $conf->set('acks', (string) $this->config['acks']);
        $conf->set('enable.idempotence', $this->config['enable_idempotence'] ? 'true' : 'false');

        $conf->setDrMsgCb(function ($kafka, $message) {
            if ($message->err) {
                logger()->error('Message delivery failed', [
                    'error' => $message->errstr(),
                ]);
            } else {
                logger()->info('Message delivered successfully', [
                    'topic' => $message->topic_name,
                    'partition' => $message->partition,
                    'offset' => $message->offset,
                ]);
            }
        });

        $this->producer = new Producer($conf);
        $this->producer->addBrokers($this->config['brokers']);
    }

    public function produce(string $topic, array $payload, string|null $key = null, array $headers = []): bool
    {
        try {
            $topic = $this->producer->newTopic($topic);

            $rdkafka_headers = [];
            foreach ($headers as $key => $value) {
                $rdkafka_headers[] = (string) $key . '=' . (string) $value;
            }

            $topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($payload), $key, json_encode($rdkafka_headers));
            $this->producer->poll(0);

            return true;
        } catch (\RdKafka\Exception $e) {
            logger()->error("Failed to produce message to Kafka: " . $e->getMessage());
            throw new \Exception("Kafka production error: " . $e->getMessage(), 0, $e);
        }
    }

    public function flush(int $timeout = 1000): void
    {
        $result = $this->producer->flush($timeout);

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            logger()->warning('Kafka producer flush timed out or failed');
        }
    }
}
