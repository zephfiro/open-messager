# Open Messager

Open Messager is a study project built with [Laravel](https://laravel.com/) that demonstrates the implementation of an outbox pattern for reliable message delivery using [Kafka](https://kafka.apache.org/). The project is designed as a learning resource for integrating Laravel applications with Kafka for event-driven architectures.

## Features

- **Outbox Pattern:** All domain events (e.g., user updates) are stored in an `outbox_messages` table before being sent to Kafka, ensuring reliable delivery.
- **Kafka Integration:** Uses a custom [`KafkaProducerService`](app/Services/KafkaProducerService.php) to produce messages to Kafka topics.
- **Outbox Processor:** Artisan command [`ProcessOutboxMessagesCommand`](app/Console/Commands/ProcessOutboxMessagesCommand.php) to process and send pending outbox messages.
- **Configurable via `.env`:** Kafka and database settings are easily configurable.


## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- php-rdkafka (PECL extension)
- Kafka (local or remote broker)
- SQLite/MySQL/MariaDB/PostgreSQL (default: SQLite)

### Installation (Docker version will be available soon)

```bash
git clone https://github.com/zephfiro/open-messager.git
cd open-messager
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev
```
