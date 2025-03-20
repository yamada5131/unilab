<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected $connection;

    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            'armadillo.rmq.cloudamqp.com',
            5672,
            'aizfhyyx',
            'LZhALcBsyDLc1pqBJNowAzFWJ_GsaSBw',
            'aizfhyyx'
        );

        $this->channel = $this->connection->channel();

        // Declare exchanges
        $this->channel->exchange_declare(
            'unilab.commands',    // exchange name
            'topic',              // type
            false,                // passive
            true,                 // durable
            false                 // auto_delete
        );
    }

    /**
     * Gửi lệnh đến một máy tính cụ thể
     */
    public function sendCommandToComputer($computerId, $roomId, $command)
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
    public function sendCommandToRoom($roomId, $command)
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
    public function publishAgentUpdate($updateInfo, $osType = null, $versionRange = null)
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
