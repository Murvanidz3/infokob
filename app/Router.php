<?php

declare(strict_types=1);

class Router
{
    /**
     * @param array<int, array{0:string,1:string,2:string}> $routes
     * @return array{handler:string, params:array<string,string>}|null
     */
    public static function match(string $method, string $path, array $routes): ?array
    {
        $path = self::normalizePath($path);
        foreach ($routes as $route) {
            [$m, $pattern, $handler] = $route;
            if (strtoupper($m) !== strtoupper($method)) {
                continue;
            }
            $regex = self::patternToRegex($pattern);
            if (preg_match($regex, $path, $mch)) {
                $params = [];
                foreach ($mch as $k => $v) {
                    if (is_string($k) && $k !== '') {
                        $params[$k] = $v;
                    }
                }
                return ['handler' => $handler, 'params' => $params];
            }
        }
        return null;
    }

    public static function normalizePath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?? '/';
        $path = '/' . trim((string) $path, '/');
        return $path === '//' ? '/' : $path;
    }

    public static function stripBasePath(string $requestUri, string $scriptName): string
    {
        $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($dir !== '' && $dir !== '/' && str_starts_with($requestUri, $dir)) {
            $requestUri = substr($requestUri, strlen($dir)) ?: '/';
        }
        return $requestUri;
    }

    private static function patternToRegex(string $pattern): string
    {
        $pattern = self::normalizePath($pattern);
        $regex = preg_replace_callback('/\{([a-z_]+)\}/i', static function (array $m): string {
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $pattern);
        return '#^' . $regex . '$#u';
    }
}
