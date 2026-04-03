<?php

declare(strict_types=1);

class User
{
    public static function countRegularUsers(): int
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('SELECT COUNT(*) FROM users WHERE role = ? AND is_active = 1');
        $st->execute(['user']);
        return (int) $st->fetchColumn();
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findById(int $id): ?array
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $st->execute([mb_strtolower(trim($email))]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function emailExists(string $email): bool
    {
        return self::findByEmail($email) !== null;
    }

    /**
     * @return int new user id
     */
    public static function create(
        string $name,
        string $email,
        string $phone,
        string $passwordHash,
        ?string $whatsapp,
        ?string $telegram
    ): int {
        $pdo = Database::getInstance();
        $st = $pdo->prepare(
            'INSERT INTO users (name, email, phone, whatsapp_number, telegram_username, password, role, is_active, email_verified)
             VALUES (?, ?, ?, ?, ?, ?, \'user\', 1, 1)'
        );
        $st->execute([
            $name,
            mb_strtolower(trim($email)),
            $phone !== '' ? $phone : null,
            $whatsapp !== null && $whatsapp !== '' ? preg_replace('/\D+/', '', $whatsapp) : null,
            $telegram !== null && $telegram !== '' ? ltrim(trim($telegram), '@') : null,
            $passwordHash,
        ]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * @param array{name?:string,email?:string,phone?:string,whatsapp_number?:string,telegram_username?:string,password?:string} $data
     */
    public static function updateProfile(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $params[] = $data['name'];
        }
        if (isset($data['email'])) {
            $fields[] = 'email = ?';
            $params[] = mb_strtolower(trim((string) $data['email']));
        }
        if (array_key_exists('phone', $data)) {
            $fields[] = 'phone = ?';
            $params[] = $data['phone'] !== '' ? $data['phone'] : null;
        }
        if (array_key_exists('whatsapp_number', $data)) {
            $fields[] = 'whatsapp_number = ?';
            $v = $data['whatsapp_number'];
            $params[] = $v !== null && $v !== '' ? preg_replace('/\D+/', '', (string) $v) : null;
        }
        if (array_key_exists('telegram_username', $data)) {
            $fields[] = 'telegram_username = ?';
            $v = $data['telegram_username'];
            $params[] = $v !== null && $v !== '' ? ltrim(trim((string) $v), '@') : null;
        }
        if (isset($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = $data['password'];
        }
        if ($fields === []) {
            return true;
        }
        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $pdo = Database::getInstance();
        $st = $pdo->prepare($sql);
        return $st->execute($params);
    }

    public static function adminCountRegular(): int
    {
        $pdo = Database::getInstance();
        $st = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $st->execute();
        return (int) $st->fetchColumn();
    }

    public static function adminTotalUsers(): int
    {
        $pdo = Database::getInstance();
        return (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function adminList(int $page, int $perPage = 40): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $pdo = Database::getInstance();
        $st = $pdo->prepare(
            'SELECT id, name, email, phone, role, is_active, created_at
             FROM users ORDER BY id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset
        );
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function adminSetUserActive(int $userId, bool $active): bool
    {
        if ($userId <= 0) {
            return false;
        }
        $pdo = Database::getInstance();
        $st = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? AND role = 'user'");
        $st->execute([$active ? 1 : 0, $userId]);
        return $st->rowCount() > 0;
    }
}
