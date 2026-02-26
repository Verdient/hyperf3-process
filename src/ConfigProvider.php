<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Hyperf\Contract\ProcessInterface;
use Hyperf\Stringable\Str;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
                ProcessListCommand::class,
                ProcessStartCommand::class
            ],
            'listeners' => [
                FailToHandleListener::class
            ],
            'logger' => [
                ProcessInterface::class => (function (string $name) {
                    $nameParts = array_map([Str::class, 'kebab'], explode('\\', Utils::simplifyName($name)));

                    $filename = BASE_PATH . '/runtime/logs/process/' . implode('/', $nameParts) . '/.log';

                    return [
                        'handler' => [
                            'class' => RotatingFileHandler::class,
                            'constructor' => [
                                'filename' => $filename,
                                'filenameFormat' => '{date}'
                            ],
                        ],
                        'formatter' => [
                            'class' => LineFormatter::class,
                            'constructor' => [
                                'format' => "%datetime% [%level_name%] %message%\n",
                                'dateFormat' => 'Y-m-d H:i:s',
                                'allowInlineLineBreaks' => true,
                            ],
                        ]
                    ];
                })->bindTo(null)
            ],
            'annotations' => [
                'scan' => [
                    'class_map' => [
                        \Hyperf\Process\AbstractProcess::class => __DIR__ . '/class_map/AbstractProcess.php',
                    ]
                ]
            ]
        ];
    }
}
