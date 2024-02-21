<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Process\AbstractProcess as ProcessAbstractProcess;
use Hyperf\Stringable\Str;

use function Hyperf\Support\env;

/**
 * 抽象进程
 * @author Verdient。
 */
abstract class AbstractProcess extends ProcessAbstractProcess
{
    use HasLogger;

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function __construct(ContainerInterface $container)
    {
        $this->name = str_replace('\\', '-', Utils::simplifyName(static::class));
        parent::__construct($container);
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function isEnable($server): bool
    {
        $envName = $this->getEnvName();
        /** @var EnablerManager */
        $enablerManager = $this->container->get(EnablerManager::class);
        $enablerManager->collect(static::class, $envName);
        return env($envName, false);
    }

    /**
     * 获取环境变量名称
     * @return string
     * @author Verdient。
     */
    protected function getEnvName(): string
    {
        return 'PROCESS_' . strtoupper(implode('_', array_map(function ($part) {
            return Str::snake($part);
        }, explode('\\', static::class))));
    }
}
