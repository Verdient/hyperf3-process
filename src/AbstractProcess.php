<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Contract\ProcessInterface;
use Hyperf\Process\AbstractProcess as ProcessAbstractProcess;
use Hyperf\Stringable\Str;
use Override;
use Verdient\Hyperf3\Logger\HasLogger;

use function Hyperf\Support\env;

/**
 * 抽象进程
 *
 * @author Verdient。
 */
abstract class AbstractProcess extends ProcessAbstractProcess
{
    use HasLogger;

    /**
     * 构造函数
     *
     * @author Verdient。
     */
    public function __construct(ContainerInterface $container)
    {
        $this->name = str_replace('\\', '-', Utils::simplifyName(static::class));
        parent::__construct($container);
    }

    /**
     * 创建默认的记录器的组名集合
     *
     * @return array<int|string,string>
     * @author Verdient。
     */
    protected function groupsForCreateDefaultLogger(): array
    {
        return [static::class => ProcessInterface::class];
    }

    /**
     * @author Verdient。
     */
    #[Override]
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
     *
     * @author Verdient。
     */
    protected function getEnvName(): string
    {
        return 'PROCESS_' . strtoupper(implode('_', array_map(function ($part) {
            return Str::snake($part);
        }, explode('\\', static::class))));
    }
}
