<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Event\EventDispatcher;
use Override;
use Verdient\Hyperf3\Di\Container;
use Verdient\Hyperf3\Exception\ExceptionOccurredEvent;

/**
 * 处理失败事件监听器
 *
 * @author Verdient。
 */
class FailToHandleListener implements ListenerInterface
{
    /**
     * @author Verdient。
     */
    #[Override]
    public function listen(): array
    {
        return [
            FailToHandle::class,
        ];
    }

    /**
     * @param FailToHandle $event
     *
     * @author Verdient。
     */
    #[Override]
    public function process(object $event): void
    {
        if ($eventDispatcher = Container::getOrNull(EventDispatcher::class)) {
            $eventDispatcher->dispatch(new ExceptionOccurredEvent($event->throwable));
        }
    }
}
