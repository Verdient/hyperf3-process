<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
                ProcessListCommand::class,
                ProcessStartCommand::class
            ]
        ];
    }
}
