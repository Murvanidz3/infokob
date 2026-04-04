<?php

declare(strict_types=1);

/**
 * Append-only JSON lists for user-submitted guide entries (classifieds, vacancies).
 */
class GuideUserContent
{
    private static function dirPath(): string
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data';
    }

    private static function filePath(string $name): string
    {
        $dir = self::dirPath();
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . DIRECTORY_SEPARATOR . $name . '.json';
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function readList(string $name): array
    {
        $p = self::filePath($name);
        if (!is_readable($p)) {
            return [];
        }
        $raw = file_get_contents($p);
        $data = json_decode($raw !== false && $raw !== '' ? $raw : '[]', true);
        return is_array($data) ? $data : [];
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function append(string $name, array $row): bool
    {
        $list = self::readList($name);
        $maxId = 0;
        foreach ($list as $item) {
            if (isset($item['id']) && is_numeric($item['id'])) {
                $maxId = max($maxId, (int) $item['id']);
            }
        }
        $row['id'] = $maxId + 1;
        $list[] = $row;
        $p = self::filePath($name);
        $json = json_encode($list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $json !== false && file_put_contents($p, $json, LOCK_EX) !== false;
    }
}
