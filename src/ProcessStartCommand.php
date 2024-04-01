<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Symfony\Component\Console\Input\InputArgument;
use Hyperf\Command\Command;

use function Hyperf\Support\make;

/**
 * 启动进程
 * @author Verdient。
 */
class ProcessStartCommand extends Command
{
    use ParseProcesses;

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function __construct(protected EnablerManager $enablerManager)
    {
        parent::__construct('process:start');
        $this->setDescription('启动进程');
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function handle()
    {
        $processes = [];

        foreach ($this->parseProcesses() as $process) {
            $key = str_replace('\\', '.', $process['class']);
            $processes[$key] = $process;
        }

        if (empty($processes)) {
            return $this->error('没有可用的进程');
        }

        $name = $this->input->getArgument('name');
        if (empty($name)) {
            $choices = [];
            $maxLength = 0;
            foreach ($processes as $key => $process) {
                $length = strlen($key);
                if ($length > $maxLength) {
                    $maxLength = $length;
                }
            }
            $map = [];
            foreach ($processes as $key => $process) {
                $description = $process['description'];
                $choices[] = $description;
                $map[$description] = $key;
            }
            $choice = $this->choice('请选择要启动的进程', $choices);

            $name = $map[$choice];
        } else {
            if (!isset($processes[$name])) {
                return $this->error('进程名称 ' . $name . ' 不匹配');
            }
        }

        $process = $processes[$name];

        $this->info('启动进程 ' . $process['class'] . ' ' . $process['description']);

        $instance = make($process['class']);

        $instance->handle();
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, '进程名称']
        ];
    }
}
