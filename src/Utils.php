<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Process;

/**
 * 工具
 * @author Verdient。
 */
class Utils
{
    /**
     * 简化名称
     * @param string $class 类名
     * @author Verdient。
     */
    public static function simplifyName(string $class): string
    {
        $namespaces = [
            'App\Process\\',
            'App\Processes\\',
        ];

        foreach ($namespaces as $namespace) {
            if (str_starts_with($class, $namespace)) {
                $class = substr($class, strlen($namespace));
                break;
            }
        }

        $suffixes = [
            'Process'
        ];

        foreach ($suffixes as $suffix) {
            $length = strlen($suffix);
            if (strlen($class) > $length && str_ends_with($class, $suffix)) {
                $class = substr($class, 0, -$length);
            }
        }

        return $class;
    }
}
