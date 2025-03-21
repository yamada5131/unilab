<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected $connection;

    protected $channel;

    public function __construct()
    {
        try {
            $this->connect();
            $this->setupExchanges();
        } catch (\Exception $e) {
            Log::error('RabbitMQ connection error: '.$e->getMessage());
            // You might want to handle this differently depending on your app's needs
        }
    }

    protected function connect(): bool
    {

        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost'),
        );

        $this->channel = $this->connection->channel();

        return true;
    }

    protected function setupExchanges()
    {
        // Declare exchanges
        foreach (config('rabbitmq.exchanges') as $exchange) {
            $this->channel->exchange_declare(
                $exchange['name'],    // exchange name
                $exchange['type'],    // type
                false,                // passive
                $exchange['durable'], // durable
                $exchange['auto_delete'] // auto_delete
            );
        }
    }

    /**
     * Gửi lệnh đến một máy tính cụ thể
     */
    public function sendCommandToComputer($computerId, $roomId, array $command): bool
    {
        $routingKey = "command.room_{$roomId}.computer_{$computerId}";

        $message = new AMQPMessage(
            json_encode($command),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'message_id' => $command['id'],
                'timestamp' => time(),
                'expiration' => (60 * 10 * 1000), // 10 phút, tính bằng milliseconds
            ]
        );

        $this->channel->basic_publish($message, 'unilab.commands', $routingKey);

        return true;
    }

    /**
     * Gửi lệnh đến tất cả máy tính trong phòng
     */
    public function sendCommandToRoom($roomId, $command): bool
    {
        $routingKey = "command.room_{$roomId}.*";

        $message = new AMQPMessage(
            json_encode($command),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $this->channel->basic_publish($message, 'unilab.commands', $routingKey);

        return true;
    }

    /**
     * Phát hành cập nhật agent mới
     */
    public function publishAgentUpdate($updateInfo, $osType = null, $versionRange = null): bool
    {
        // Publish to update exchange
        $routingKey = $osType ? "updates.{$osType}.{$versionRange}" : 'updates.all';

        $message = new AMQPMessage(
            json_encode($updateInfo),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $this->channel->basic_publish($message, 'unilab.updates', $routingKey);

        return true;
    }

    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }

        if ($this->connection) {
            $this->connection->close();
        }
    }
}
