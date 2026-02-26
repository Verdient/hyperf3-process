<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Hyperf\Contract\ProcessInterface;
use Throwable;

/**
 * 处理失败
 *
 * @author Verdient。
 */
class FailToHandle
{
    /**
     * @param ProcessInterface $process 进程
     * @param Throwable $throwable 异常
     */
    public function __construct(
        public readonly ProcessInterface $process,
        public readonly Throwable $throwable
    ) {}
}
