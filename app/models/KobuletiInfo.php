<?php

declare(strict_types=1);

class KobuletiInfo
{
    /**
     * @return list<array{section:string,title:?string,content:?string}>
     */
    public static function getByLang(string $lang): array
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('SELECT section, title, content FROM kobuleti_info WHERE lang = ? ORDER BY section ASC');
        $st->execute([$lang]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
