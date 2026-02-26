<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Override;
use Swoole\Coroutine\System;
use Verdient\Hyperf3\Di\Container;
use Verdient\Hyperf3\Event\Event;

use function Hyperf\Config\config;

/**
 * 抽象循环进程
 *
 * @author Verdient。
 */
abstract class AbstractLoopProcess extends AbstractProcess
{
    /**
     * @var int|int[] 休眠时间
     *
     * @author Verdient。
     */
    protected int|array $sleep = 0;

    /**
     * 是否是Debug模式
     *
     * @author Verdient。
     */
    protected ?bool $isDebug = null;

    /**
     * 获取是否是Debug模式
     *
     * @author Verdient。
     */
    protected function isDebug(): bool
    {
        if ($this->isDebug === null) {
            $this->isDebug = (bool) config('debug', false);
        }

        return $this->isDebug;
    }

    /**
     * @author Verdient。
     */
    #[Override]
    public function handle(): void
    {
        while (true) {
            try {
                $this->execute();
            } catch (\Throwable $e) {

                $this->logger()->error($e);

                if ($this->isDebug()) {
                    if ($logger = Container::getOrNull(StdoutLoggerInterface::class)) {
                        $formatter = Container::getOrNull(FormatterInterface::class);
                        $logger->error($formatter ? $formatter->format($e) : $e);
                    }
                }

                Event::dispatch(new FailToHandle($this, $e));
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
     * 执行单次任务
     *
     * @author Verdient。
     */
    abstract protected function execute(): void;
}
