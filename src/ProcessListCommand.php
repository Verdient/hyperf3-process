<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Hyperf\Command\Command;
use Verdient\cli\Console;

/**
 * 展示所有可用的进程
 * @author Verdient。
 */
class ProcessListCommand extends Command
{
    use ParseProcesses;

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function __construct(protected EnablerManager $enablerManager)
    {
        parent::__construct('process:list');
        $this->setDescription('展示进程列表');
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function handle()
    {
        $data = [];

        foreach ($this->parseProcesses() as $process) {
            if (is_bool($process['enable'])) {
                $enable = $process['enable'] ? '是' : '否';
            } else {
                $enable = $process['enable'];
            }

            $data[] = [
                str_replace('\\', '.', $process['class']),
                $process['description'],
                $enable,
                $this->enablerManager->getEnablerName($process['class'])
            ];
        }

        Console::table($data, ['名称', '描述', '启用', '开关名称（环境变量）']);
    }
}
