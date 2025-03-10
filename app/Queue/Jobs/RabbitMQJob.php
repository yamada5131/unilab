<?php

namespace App\Queue\Jobs;

use Illuminate\Support\Facades\Log;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob as BaseRabbitMQJob;

class RabbitMQJob extends BaseRabbitMQJob
{
    /**
     * Ghi đè phương thức payload() để trả về payload JSON thuần.
     *
     * @return array
     */
    public function payload()
    {
        // Giả sử job của bạn chứa một đối tượng Command với các thông tin cần thiết
        // Bạn cần chuyển đổi đối tượng này thành mảng thuần túy để dễ chuyển sang JSON
        $payload = $this->getRawBody();
        $commandData = json_decode($payload, true);

        if (! $commandData) {
            Log::error('RabbitMQJob: Không thể parse payload JSON.');

            return [];
        }

        Log::info('RabbitMQJob: Đã parse payload JSON thành công.', $commandData);

        return [
            'job' => 'App\\Jobs\\ProcessComputerCommand@handle',
            'data' => [
                'machine_id' => $commandData['machine_id'] ?? null,
                'command_type' => $commandData['command_type'] ?? null,
                'data' => [
                    'id' => $commandData['id'] ?? null,
                ],
            ],
        ];
    }

    /**
     * Nếu cần, bạn cũng có thể ghi đè phương thức fire() để xử lý message theo cách riêng.
     * Trong ví dụ dưới đây, chúng ta chỉ log payload và sau đó xóa job.
     *
     * @return void
     */
    public function fire()
    {
        $payload = $this->payload();

        // Ghi log payload để debug
        Log::info('RabbitMQJob xử lý payload:', $payload);

        // Gọi đến handler thực tế của job
        $jobInstance = app()->make('App\\Jobs\\ProcessComputerCommand');
        $jobInstance->handle($payload['data']);

        // Xóa job sau khi xử lý thành công
        $this->delete();
    }
}
