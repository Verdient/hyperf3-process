<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

use Hyperf\Contract\ProcessInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\ReflectionManager;
use Hyperf\Process\Annotation\Process;
use phpDocumentor\Reflection\DocBlockFactory;
use Swoole\Coroutine\Server as CoroutineServer;
use Swoole\Server;

use function Hyperf\Config\config;
use function Hyperf\Support\make;

/**
 * 解析进程
 * @author Verdient。
 */
trait ParseProcesses
{
    /**
     * 解析进程
     * @return Crontab[]
     * @author Verdient。
     */
    protected function parseProcesses(): array
    {
        $configProcesses = config('processes', []);

        $annotationProcesses = AnnotationCollector::getClassesByAnnotation(Process::class);

        $processes = array_merge($configProcesses, array_keys($annotationProcesses));

        $result = [];

        $docBlockFactory = DocBlockFactory::createInstance();

        $server = new Server('127.0.0.1');

        $coroutineServer = new CoroutineServer('127.0.0.1');

        foreach ($processes as $class) {

            if (!is_string($class)) {
                continue;
            }

            $instance = make($class);

            if (!$instance instanceof ProcessInterface) {
                continue;
            }

            $reflectClass = ReflectionManager::reflectClass($class);
            $docComment = $reflectClass->getDocComment();
            $serverEnable = $instance->isEnable($server);
            $coroutineServerEnable = $instance->isEnable($coroutineServer);

            if ($serverEnable && $coroutineServerEnable) {
                $enable = true;
            } else {
                if ($serverEnable) {
                    $enable = Server::class;
                } else if ($coroutineServerEnable) {
                    $enable = CoroutineServer::class;
                } else {
                    $enable = false;
                }
            }

            $result[] = [
                'class' => $class,
                'description' => $docComment ? $docBlockFactory->create($docComment)->getSummary() : '',
                'enable' => $enable,
            ];
        }

        return $result;
    }
}
