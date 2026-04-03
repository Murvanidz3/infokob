<?php

declare(strict_types=1);

class Property
{
    public const PER_PAGE = 12;

    public static function deactivateExpiredFeatured(): void
    {
        $pdo = Database::getInstance();
        $pdo->exec(
            'UPDATE properties SET is_featured = 0, featured_until = NULL
             WHERE is_featured = 1 AND featured_until IS NOT NULL AND featured_until < NOW()'
        );
    }

    public static function countActive(): int
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE status = 'active'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public static function countSold(): int
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE status = 'sold'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public static function countNearSea(int $maxMeters): int
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare(
            'SELECT COUNT(*) FROM properties WHERE status = ? AND sea_distance_m IS NOT NULL AND sea_distance_m <= ?'
        );
        $st->execute(['active', $maxMeters]);
        return (int) $st->fetchColumn();
    }

    /**
     * @return array<string, mixed>
     */
    public static function parseFiltersFromRequest(array $get): array
    {
        $deal = (string) ($get['deal'] ?? 'sale');
        if (!in_array($deal, ['sale', 'rent', 'daily_rent'], true)) {
            $deal = 'sale';
        }
        $types = $get['types'] ?? [];
        if (!is_array($types)) {
            $types = $types !== '' && $types !== null ? [(string) $types] : [];
        }
        $allowedTypes = ['apartment', 'house', 'cottage', 'land', 'commercial', 'hotel_room'];
        $types = array_values(array_intersect($allowedTypes, array_map('strval', $types)));
        if (!empty($get['type'])) {
            $t = (string) $get['type'];
            if (in_array($t, $allowedTypes, true)) {
                $types[] = $t;
            }
        }
        $types = array_values(array_unique($types));

        $priceMin = isset($get['price_min']) && $get['price_min'] !== '' ? (float) $get['price_min'] : null;
        $priceMax = isset($get['price_max']) && $get['price_max'] !== '' ? (float) $get['price_max'] : null;

        $rooms = isset($get['rooms']) && $get['rooms'] !== '' ? (string) $get['rooms'] : '';
        if ($rooms === '5+') {
            $rooms = '5';
        }

        $sea = isset($get['sea']) ? (string) $get['sea'] : '';
        $seaMax = null;
        if ($sea !== '' && $sea !== 'any') {
            $seaMax = (int) $sea;
        }

        $district = isset($get['district']) ? trim((string) $get['district']) : '';

        $sort = (string) ($get['sort'] ?? 'newest');
        if (!in_array($sort, ['newest', 'price_asc', 'price_desc'], true)) {
            $sort = 'newest';
        }

        $q = isset($get['q']) ? trim((string) $get['q']) : '';

        return [
            'deal_type' => $deal,
            'types' => $types,
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'rooms' => $rooms,
            'sea_max' => $seaMax,
            'district' => $district,
            'sort' => $sort,
            'q' => $q,
        ];
    }

    /**
     * WHERE conditions (without JOIN lang — language is bound in JOIN).
     *
     * @param array<string, mixed> $filters
     * @return array{sql:string, params:array<int, mixed>}
     */
    private static function buildWhere(array $filters): array
    {
        $params = [];
        $sql = " p.status = 'active' ";

        $sql .= ' AND p.deal_type = ? ';
        $params[] = $filters['deal_type'];

        if (!empty($filters['types'])) {
            $placeholders = implode(',', array_fill(0, count($filters['types']), '?'));
            $sql .= " AND p.type IN ($placeholders) ";
            foreach ($filters['types'] as $t) {
                $params[] = $t;
            }
        }

        if ($filters['price_min'] !== null) {
            $sql .= ' AND p.price >= ? ';
            $params[] = $filters['price_min'];
        }
        if ($filters['price_max'] !== null) {
            $sql .= ' AND p.price <= ? ';
            $params[] = $filters['price_max'];
        }

        if ($filters['rooms'] !== '') {
            if ($filters['rooms'] === '5') {
                $sql .= ' AND p.rooms IS NOT NULL AND p.rooms >= 5 ';
            } else {
                $sql .= ' AND p.rooms = ? ';
                $params[] = (int) $filters['rooms'];
            }
        }

        if ($filters['sea_max'] !== null) {
            $sql .= ' AND p.sea_distance_m IS NOT NULL AND p.sea_distance_m <= ? ';
            $params[] = $filters['sea_max'];
        }

        if ($filters['district'] !== '' && $filters['district'] !== 'all') {
            $sql .= ' AND p.district = ? ';
            $params[] = $filters['district'];
        }

        if ($filters['q'] !== '') {
            $sql .= ' AND (pt.title LIKE ? OR pt.description LIKE ?) ';
            $like = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        return ['sql' => $sql, 'params' => $params];
    }

    /**
     * @param array<string, mixed> $filters
     */
    public static function countFiltered(array $filters, string $lang): int
    {
        $w = self::buildWhere($filters);
        $sql = 'SELECT COUNT(DISTINCT p.id) FROM properties p
                INNER JOIN property_translations pt ON p.id = pt.property_id AND pt.lang = ?
                WHERE ' . $w['sql'];
        $params = array_merge([$lang], $w['params']);
        $pdo = Database::getInstance();
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return (int) $st->fetchColumn();
    }

    /**
     * @param array<string, mixed> $filters
     * @return list<array<string, mixed>>
     */
    public static function getFiltered(array $filters, string $lang, int $page, int $perPage = self::PER_PAGE): array
    {
        $w = self::buildWhere($filters);
        $order = match ($filters['sort']) {
            'price_asc' => 'p.is_featured DESC, p.price IS NULL, p.price ASC, p.id DESC',
            'price_desc' => 'p.is_featured DESC, p.price IS NULL, p.price DESC, p.id DESC',
            default => 'p.is_featured DESC, p.created_at DESC',
        };
        $offset = max(0, ($page - 1) * $perPage);
        $sql = 'SELECT p.*, pt.title, pt.description,
                pi.filename AS main_image
                FROM properties p
                INNER JOIN property_translations pt ON p.id = pt.property_id AND pt.lang = ?
                LEFT JOIN property_images pi ON pi.id = (
                    SELECT pi2.id FROM property_images pi2 WHERE pi2.property_id = p.id
                    ORDER BY pi2.is_main DESC, pi2.sort_order ASC, pi2.id ASC LIMIT 1
                )
                WHERE ' . $w['sql'] . '
                ORDER BY ' . $order . '
                LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;

        $params = array_merge([$lang], $w['params']);
        $pdo = Database::getInstance();
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Featured first; if none, fall back to newest active (handled in PHP if needed).
     *
     * @return list<array<string, mixed>>
     */
    public static function getFeaturedForHome(string $lang, int $limit = 6): array
    {
        $pdo = Database::getInstance();
        $sql = 'SELECT p.*, pt.title, pt.description, pi.filename AS main_image
            FROM properties p
            INNER JOIN property_translations pt ON p.id = pt.property_id AND pt.lang = ?
            LEFT JOIN property_images pi ON pi.id = (
                SELECT pi2.id FROM property_images pi2 WHERE pi2.property_id = p.id
                ORDER BY pi2.is_main DESC, pi2.sort_order ASC, pi2.id ASC LIMIT 1
            )
            WHERE p.status = ?
              AND p.is_featured = 1
              AND (p.featured_until IS NULL OR p.featured_until > NOW())
            ORDER BY p.featured_until IS NULL DESC, p.featured_until DESC, p.created_at DESC
            LIMIT ' . (int) $limit;
        $st = $pdo->prepare($sql);
        $st->execute([$lang, 'active']);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) >= $limit) {
            return array_slice($rows, 0, $limit);
        }
        $need = $limit - count($rows);
        $exclude = array_map('intval', array_column($rows, 'id'));
        $fallback = self::getNewestActiveExcluding($lang, $need, $exclude);
        return array_merge($rows, $fallback);
    }

    /**
     * @param list<int|string> $excludeIds
     * @return list<array<string, mixed>>
     */
    private static function getNewestActiveExcluding(string $lang, int $limit, array $excludeIds): array
    {
        if ($limit <= 0) {
            return [];
        }
        $params = [$lang, 'active'];
        $sql = 'SELECT p.*, pt.title, pt.description, pi.filename AS main_image
            FROM properties p
            INNER JOIN property_translations pt ON p.id = pt.property_id AND pt.lang = ?
            LEFT JOIN property_images pi ON pi.id = (
                SELECT pi2.id FROM property_images pi2 WHERE pi2.property_id = p.id
                ORDER BY pi2.is_main DESC, pi2.sort_order ASC, pi2.id ASC LIMIT 1
            )
            WHERE p.status = ? ';
        if ($excludeIds !== []) {
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $sql .= " AND p.id NOT IN ($placeholders) ";
            foreach ($excludeIds as $id) {
                $params[] = (int) $id;
            }
        }
        $sql .= ' ORDER BY p.created_at DESC LIMIT ' . (int) $limit;
        $pdo = Database::getInstance();
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function getPublicBySlug(string $slug, string $lang): ?array
    {
        $pdo = Database::getInstance();
        $sql = 'SELECT p.*, pt.title, pt.description,
                pi.filename AS main_image,
                u.whatsapp_number AS user_whatsapp,
                u.telegram_username AS user_telegram,
                u.name AS user_name,
                u.email AS user_email,
                u.phone AS user_phone
            FROM properties p
            INNER JOIN property_translations pt ON p.id = pt.property_id AND pt.lang = ?
            INNER JOIN users u ON u.id = p.user_id
            LEFT JOIN property_images pi ON pi.id = (
                SELECT pi2.id FROM property_images pi2 WHERE pi2.property_id = p.id
                ORDER BY pi2.is_main DESC, pi2.sort_order ASC, pi2.id ASC LIMIT 1
            )
            WHERE p.slug = ? AND p.status = ?';
        $st = $pdo->prepare($sql);
        $st->execute([$lang, $slug, 'active']);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function getImages(int $propertyId): array
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('SELECT * FROM property_images WHERE property_id = ? ORDER BY is_main DESC, sort_order ASC, id ASC');
        $st->execute([$propertyId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function incrementViews(int $id): void
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('UPDATE properties SET views = views + 1 WHERE id = ? AND status = ?');
        $st->execute([$id, 'active']);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function getSimilar(int $propertyId, string $district, string $type, string $lang, int $limit = 3): array
    {
        $pdo = Database::getInstance();
        $sql = 'SELECT p.*, pt.title, pt.description, pi.filename AS main_image
            FROM properties p
            INNER JOIN property_translations pt ON p.id = pt.property_id AND pt.lang = ?
            LEFT JOIN property_images pi ON pi.id = (
                SELECT pi2.id FROM property_images pi2 WHERE pi2.property_id = p.id
                ORDER BY pi2.is_main DESC, pi2.sort_order ASC, pi2.id ASC LIMIT 1
            )
            WHERE p.status = ? AND p.id != ? AND (p.district = ? OR p.type = ?)
            ORDER BY (p.district = ?) DESC, p.created_at DESC
            LIMIT ' . (int) $limit;
        $st = $pdo->prepare($sql);
        $st->execute([$lang, 'active', $propertyId, $district, $type, $district]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array{total:int,active:int,pending:int,rejected:int,views:int}
     */
    public static function getDashboardStats(int $userId): array
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare(
            'SELECT COUNT(*) AS total,
                    COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) AS active,
                    COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) AS pending,
                    COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) AS rejected,
                    COALESCE(SUM(views), 0) AS views
             FROM properties WHERE user_id = ?'
        );
        $st->execute(['active', 'pending', 'rejected', $userId]);
        $r = $st->fetch(PDO::FETCH_ASSOC) ?: [];
        return [
            'total' => (int) ($r['total'] ?? 0),
            'active' => (int) ($r['active'] ?? 0),
            'pending' => (int) ($r['pending'] ?? 0),
            'rejected' => (int) ($r['rejected'] ?? 0),
            'views' => (int) ($r['views'] ?? 0),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function listForUser(int $userId, string $lang): array
    {
        $pdo = Database::getInstance();
        $sql = 'SELECT p.*, pt.title,
                pi.filename AS main_image
            FROM properties p
            INNER JOIN property_translations pt ON p.id = pt.property_id AND pt.lang = ?
            LEFT JOIN property_images pi ON pi.id = (
                SELECT pi2.id FROM property_images pi2 WHERE pi2.property_id = p.id
                ORDER BY pi2.is_main DESC, pi2.sort_order ASC, pi2.id ASC LIMIT 1
            )
            WHERE p.user_id = ?
            ORDER BY p.created_at DESC';
        $st = $pdo->prepare($sql);
        $st->execute([$lang, $userId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function getOwnedById(int $propertyId, int $userId): ?array
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('SELECT * FROM properties WHERE id = ? AND user_id = ? LIMIT 1');
        $st->execute([$propertyId, $userId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * @return array<string, array{title:string,description:string}>
     */
    public static function getTranslationsMap(int $propertyId): array
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('SELECT lang, title, description FROM property_translations WHERE property_id = ?');
        $st->execute([$propertyId]);
        $out = [];
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $out[(string) $row['lang']] = [
                'title' => (string) $row['title'],
                'description' => (string) ($row['description'] ?? ''),
            ];
        }
        return $out;
    }

    public static function generateUniqueSlug(string $titleBase): string
    {
        $base = Helpers::slug($titleBase);
        if ($base === '') {
            $base = 'listing';
        }
        $pdo = Database::getInstance();
        $candidate = $base;
        $n = 2;
        while (true) {
            $st = $pdo->prepare('SELECT COUNT(*) FROM properties WHERE slug = ?');
            $st->execute([$candidate]);
            if ((int) $st->fetchColumn() === 0) {
                return $candidate;
            }
            $candidate = $base . '-' . $n;
            $n++;
        }
    }

    /**
     * @param array<string, mixed> $data property columns (no id/user_id/slug)
     * @param array<string, array{title:string,description:string}> $translations
     * @param list<array{tmp_name:string,error:int,name:string,size:int,type:string}> $files
     */
    public static function createForUser(int $userId, string $slug, array $data, array $translations, array $files): int
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();
        try {
            $st = $pdo->prepare(
                'INSERT INTO properties (
                    user_id, slug, type, deal_type, status, price, currency, price_negotiable,
                    area_m2, rooms, bedrooms, bathrooms, floors_total, floor_number,
                    has_pool, has_garage, has_balcony, has_garden, sea_distance_m,
                    address, district, lat, lng,
                    contact_name, contact_phone, contact_whatsapp, contact_email, contact_telegram,
                    is_featured, featured_until, featured_paid, views
                ) VALUES (
                    ?, ?, ?, ?, \'pending\', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NULL, 0, 0
                )'
            );
            $st->execute([
                $userId,
                $slug,
                $data['type'],
                $data['deal_type'],
                $data['price'],
                $data['currency'],
                $data['price_negotiable'],
                $data['area_m2'],
                $data['rooms'],
                $data['bedrooms'],
                $data['bathrooms'],
                $data['floors_total'],
                $data['floor_number'],
                $data['has_pool'],
                $data['has_garage'],
                $data['has_balcony'],
                $data['has_garden'],
                $data['sea_distance_m'],
                $data['address'],
                $data['district'],
                $data['lat'],
                $data['lng'],
                $data['contact_name'],
                $data['contact_phone'],
                $data['contact_whatsapp'],
                $data['contact_email'],
                $data['contact_telegram'],
            ]);
            $propertyId = (int) $pdo->lastInsertId();

            $pt = $pdo->prepare(
                'INSERT INTO property_translations (property_id, lang, title, description) VALUES (?, ?, ?, ?)'
            );
            foreach (['ka', 'ru', 'en'] as $lang) {
                $t = $translations[$lang] ?? $translations['ka'];
                $pt->execute([$propertyId, $lang, $t['title'], $t['description']]);
            }

            self::saveUploadedImages($pdo, $propertyId, $files);

            $imgCount = (int) $pdo->query('SELECT COUNT(*) FROM property_images WHERE property_id = ' . $propertyId)->fetchColumn();
            if ($imgCount < 1) {
                throw new RuntimeException('no_images');
            }

            $pdo->commit();
            return $propertyId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * @param list<array{tmp_name:string,error:int,name:string,size:int,type:string}> $files
     */
    private static function saveUploadedImages(PDO $pdo, int $propertyId, array $files): void
    {
        if ($files === []) {
            return;
        }
        $st = $pdo->prepare(
            'INSERT INTO property_images (property_id, filename, is_main, sort_order) VALUES (?, ?, ?, ?)'
        );
        foreach ($files as $idx => $file) {
            $v = Image::validateUpload($file);
            if ($v === null) {
                continue;
            }
            $saved = Image::processAndSave($file['tmp_name'], $v['base'], $v['ext']);
            if ($saved === null) {
                continue;
            }
            $st->execute([$propertyId, $saved, $idx === 0 ? 1 : 0, $idx]);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, array{title:string,description:string}> $translations
     * @param list<array{tmp_name:string,error:int,name:string,size:int,type:string}> $newFiles
     * @param list<int> $deleteImageIds
     */
    public static function updateForUser(
        int $propertyId,
        int $userId,
        string $slug,
        array $data,
        array $translations,
        array $newFiles,
        array $deleteImageIds
    ): void {
        $pdo = Database::getInstance();
        $own = self::getOwnedById($propertyId, $userId);
        if ($own === null) {
            throw new InvalidArgumentException('not_found');
        }

        $pdo->beginTransaction();
        try {
            $st = $pdo->prepare(
                'UPDATE properties SET
                    slug = ?, type = ?, deal_type = ?, price = ?, currency = ?, price_negotiable = ?,
                    area_m2 = ?, rooms = ?, bedrooms = ?, bathrooms = ?, floors_total = ?, floor_number = ?,
                    has_pool = ?, has_garage = ?, has_balcony = ?, has_garden = ?, sea_distance_m = ?,
                    address = ?, district = ?, lat = ?, lng = ?,
                    contact_name = ?, contact_phone = ?, contact_whatsapp = ?, contact_email = ?, contact_telegram = ?
                WHERE id = ? AND user_id = ?'
            );
            $st->execute([
                $slug,
                $data['type'],
                $data['deal_type'],
                $data['price'],
                $data['currency'],
                $data['price_negotiable'],
                $data['area_m2'],
                $data['rooms'],
                $data['bedrooms'],
                $data['bathrooms'],
                $data['floors_total'],
                $data['floor_number'],
                $data['has_pool'],
                $data['has_garage'],
                $data['has_balcony'],
                $data['has_garden'],
                $data['sea_distance_m'],
                $data['address'],
                $data['district'],
                $data['lat'],
                $data['lng'],
                $data['contact_name'],
                $data['contact_phone'],
                $data['contact_whatsapp'],
                $data['contact_email'],
                $data['contact_telegram'],
                $propertyId,
                $userId,
            ]);

            $pt = $pdo->prepare(
                'UPDATE property_translations SET title = ?, description = ? WHERE property_id = ? AND lang = ?'
            );
            foreach (['ka', 'ru', 'en'] as $lang) {
                $t = $translations[$lang] ?? $translations['ka'];
                $pt->execute([$t['title'], $t['description'], $propertyId, $lang]);
            }

            foreach ($deleteImageIds as $imgId) {
                $imgId = (int) $imgId;
                if ($imgId <= 0) {
                    continue;
                }
                $q = $pdo->prepare('SELECT filename FROM property_images WHERE id = ? AND property_id = ?');
                $q->execute([$imgId, $propertyId]);
                $fn = $q->fetchColumn();
                if ($fn) {
                    Image::deleteFiles((string) $fn);
                    $pdo->prepare('DELETE FROM property_images WHERE id = ?')->execute([$imgId]);
                }
            }

            if ($newFiles !== []) {
                $maxSort = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), -1) FROM property_images WHERE property_id = ' . (int) $propertyId)->fetchColumn();
                $cnt = (int) $pdo->query('SELECT COUNT(*) FROM property_images WHERE property_id = ' . (int) $propertyId)->fetchColumn();
                $hasAny = $cnt > 0;
                $st = $pdo->prepare(
                    'INSERT INTO property_images (property_id, filename, is_main, sort_order) VALUES (?, ?, ?, ?)'
                );
                $idx = 0;
                foreach ($newFiles as $file) {
                    $v = Image::validateUpload($file);
                    if ($v === null) {
                        continue;
                    }
                    $saved = Image::processAndSave($file['tmp_name'], $v['base'], $v['ext']);
                    if ($saved === null) {
                        continue;
                    }
                    $sort = $maxSort + 1 + $idx;
                    $isMain = !$hasAny && $idx === 0 ? 1 : 0;
                    $st->execute([$propertyId, $saved, $isMain, $sort]);
                    $hasAny = true;
                    $idx++;
                }
            }

            self::normalizeMainImage($propertyId);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Fix main image flags: first image by sort is main.
     */
    public static function normalizeMainImage(int $propertyId): void
    {
        $pdo = Database::getInstance();
        $pdo->exec('UPDATE property_images SET is_main = 0 WHERE property_id = ' . (int) $propertyId);
        $st = $pdo->query(
            'SELECT id FROM property_images WHERE property_id = ' . (int) $propertyId . ' ORDER BY sort_order ASC, id ASC LIMIT 1'
        );
        $first = $st->fetchColumn();
        if ($first) {
            $pdo->prepare('UPDATE property_images SET is_main = 1 WHERE id = ?')->execute([(int) $first]);
        }
    }

    public static function deleteForUser(int $propertyId, int $userId): bool
    {
        $pdo = Database::getInstance();
        if (self::getOwnedById($propertyId, $userId) === null) {
            return false;
        }
        $st = $pdo->prepare('SELECT filename FROM property_images WHERE property_id = ?');
        $st->execute([$propertyId]);
        while ($fn = $st->fetchColumn()) {
            Image::deleteFiles((string) $fn);
        }
        $del = $pdo->prepare('DELETE FROM properties WHERE id = ? AND user_id = ?');
        $del->execute([$propertyId, $userId]);
        return $del->rowCount() > 0;
    }

    public static function setStatusForUser(int $propertyId, int $userId, string $status): bool
    {
        if (!in_array($status, ['sold', 'archived'], true)) {
            return false;
        }
        $pdo = Database::getInstance();
        $st = $pdo->prepare('UPDATE properties SET status = ? WHERE id = ? AND user_id = ?');
        $st->execute([$status, $propertyId, $userId]);
        return $st->rowCount() > 0;
    }

    /**
     * @return array<string, int>
     */
    public static function adminCountByStatus(): array
    {
        $pdo = Database::getInstance();
        $st = $pdo->query("SELECT status, COUNT(*) AS c FROM properties GROUP BY status");
        $out = [
            'pending' => 0,
            'active' => 0,
            'rejected' => 0,
            'sold' => 0,
            'archived' => 0,
        ];
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $s = (string) ($row['status'] ?? '');
            if (isset($out[$s])) {
                $out[$s] = (int) $row['c'];
            }
        }
        return $out;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function getByIdForAdmin(int $id): ?array
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare(
            'SELECT p.*, u.name AS owner_name, u.email AS owner_email
             FROM properties p
             INNER JOIN users u ON u.id = p.user_id
             WHERE p.id = ? LIMIT 1'
        );
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * @param array{status?:string,q?:string} $filters
     */
    public static function adminCountFiltered(array $filters): int
    {
        [$whereSql, $params] = self::adminBuildListWhere($filters);
        $pdo = Database::getInstance();
        $sql = 'SELECT COUNT(DISTINCT p.id) FROM properties p
                INNER JOIN users u ON u.id = p.user_id
                LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = ?
                WHERE ' . $whereSql;
        array_unshift($params, 'ka');
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return (int) $st->fetchColumn();
    }

    /**
     * @param array{status?:string,q?:string} $filters
     * @return list<array<string, mixed>>
     */
    public static function adminListFiltered(array $filters, int $page, int $perPage = 25): array
    {
        [$whereSql, $params] = self::adminBuildListWhere($filters);
        $offset = max(0, ($page - 1) * $perPage);
        $pdo = Database::getInstance();
        $sql = 'SELECT p.id, p.slug, p.status, p.type, p.deal_type, p.price, p.currency, p.is_featured, p.featured_until,
                p.created_at, p.views,
                pt.title, u.name AS owner_name, u.email AS owner_email,
                pi.filename AS main_image
                FROM properties p
                INNER JOIN users u ON u.id = p.user_id
                LEFT JOIN property_translations pt ON pt.property_id = p.id AND pt.lang = ?
                LEFT JOIN property_images pi ON pi.id = (
                    SELECT pi2.id FROM property_images pi2 WHERE pi2.property_id = p.id
                    ORDER BY pi2.is_main DESC, pi2.sort_order ASC, pi2.id ASC LIMIT 1
                )
                WHERE ' . $whereSql . '
                ORDER BY p.created_at DESC
                LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;
        array_unshift($params, 'ka');
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array{status?:string,q?:string} $filters
     * @return array{0:string,1:array<int, mixed>}
     */
    private static function adminBuildListWhere(array $filters): array
    {
        $where = ['1=1'];
        $params = [];
        $status = isset($filters['status']) ? (string) $filters['status'] : 'all';
        if ($status !== 'all' && in_array($status, ['pending', 'active', 'rejected', 'sold', 'archived'], true)) {
            $where[] = 'p.status = ?';
            $params[] = $status;
        }
        $q = isset($filters['q']) ? trim((string) $filters['q']) : '';
        if ($q !== '') {
            $where[] = '(p.slug LIKE ? OR COALESCE(p.contact_phone, \'\') LIKE ? OR COALESCE(pt.title, \'\') LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        return [implode(' AND ', $where), $params];
    }

    public static function adminSetStatus(int $id, string $status, ?string $adminNote): bool
    {
        if (!in_array($status, ['pending', 'active', 'rejected', 'sold', 'archived'], true)) {
            return false;
        }
        $pdo = Database::getInstance();
        if ($status === 'rejected') {
            $note = $adminNote !== null ? trim($adminNote) : '';
            $st = $pdo->prepare('UPDATE properties SET status = ?, admin_note = ? WHERE id = ?');
            $st->execute([$status, $note !== '' ? $note : null, $id]);
        } else {
            $st = $pdo->prepare('UPDATE properties SET status = ?, admin_note = NULL WHERE id = ?');
            $st->execute([$status, $id]);
        }
        return $st->rowCount() > 0;
    }

    public static function adminToggleFeatured(int $id): bool
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('SELECT id, status, is_featured FROM properties WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return false;
        }
        if (($row['status'] ?? '') !== 'active') {
            return false;
        }
        $on = !((int) ($row['is_featured'] ?? 0));
        if ($on) {
            $days = (int) Setting::get('featured_duration_days', '30');
            if ($days < 1) {
                $days = 30;
            }
            $u = $pdo->prepare(
                'UPDATE properties SET is_featured = 1, featured_until = DATE_ADD(NOW(), INTERVAL ? DAY), featured_paid = 0 WHERE id = ?'
            );
            $u->execute([$days, $id]);
        } else {
            $u = $pdo->prepare('UPDATE properties SET is_featured = 0, featured_until = NULL WHERE id = ?');
            $u->execute([$id]);
        }
        return true;
    }
}
