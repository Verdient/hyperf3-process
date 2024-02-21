<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Psr\EventDispatcher\EventDispatcherInterface;
use Swoole\Coroutine\System;
use Verdient\cli\Console;
use Verdient\Hyperf3\Exception\ExceptionOccurredEvent;

use function Hyperf\Config\config;

/**
 * 抽象循环进程
 * @author Verdient。
 */
abstract class AbstractLoopProcess extends AbstractProcess
{
    /**
     * @var int|array 休眠时间
     * @author Verdient。
     */
    protected $sleep = 0;

    /**
     * 是否是Debug模式
     * @author Verdient。
     */
    protected bool|null $isDebug = null;

    /**
     * 获取是否在Debug模式
     * @return bool
     * @author Verdient。
     */
    protected function getIsDebug(): bool
    {
        if ($this->isDebug === null) {
            $this->isDebug = (bool) config('debug', false);
        }
        return $this->isDebug;
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function handle(): void
    {
        while (true) {
            try {
                $this->execute();
            } catch (\Throwable $e) {
                $this->logger()->error($e);
                if ($this->getIsDebug()) {
                    Console::error($e->__toString(), Console::FG_RED);
                }
                /** @var EventDispatcherInterface */
                if ($eventDispatcher = $this
                    ->container
                    ->get(EventDispatcherInterface::class)
                ) {
                    $eventDispatcher->dispatch(new ExceptionOccurredEvent($e));
                }
            }
            if (is_array($this->sleep)) {
                $sleep = random_int($this->sleep[0], $this->sleep[1]);
            } else {
                $sleep = $this->sleep;
            }
            if ($sleep) {
                $this->logger()->debug('Sleep ' . $sleep . ' seconds');
                System::sleep($sleep);
            }
        }
    }

    /**
     * 运行
     * @author Verdient。
     */
    abstract protected function execute();
}
