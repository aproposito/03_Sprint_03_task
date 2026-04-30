<?php

declare(strict_types=1);

class TagMysql 
{

    private static function _getConnection(): PDO
    {
        $settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
        return new PDO(
            sprintf(
                "%s:host=%s;dbname=%s;charset=utf8mb4",
                $settings['database']['driver'],
                $settings['database']['host'],
                $settings['database']['dbname']
            ),
            $settings['database']['user'],
            $settings['database']['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function getAll(): array
    {
        $pdo = self::_getConnection();
        $stmt = $pdo->query("SELECT * FROM tags");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function fetchOne(int $id): ?array
    {
        $pdo = self::_getConnection();
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function save(array $tag): void
    {
        $pdo = self::_getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO tags (name, color, icon, user_id) VALUES (:name, :color, :icon, :user_id)"
        );
        $stmt->execute([
            'name'    => $tag['name'],
            'color'   => $tag['color'] ?? '#D3D3D3',
            'icon'    => $tag['icon'] ?? '⭐',
            'user_id' => $tag['user_id'] ?? null,
        ]);
    }


    public static function update(array $tag): void
    {
        $pdo = self::_getConnection();
        $stmt = $pdo->prepare(
            "UPDATE tags SET name = :name, color = :color, icon = :icon WHERE id = :id"
        );
        $stmt->execute([
            'name'  => $tag['name'],
            'color' => $tag['color'],
            'icon'  => $tag['icon'],
            'id'    => $tag['id'],
        ]);
    }


    public static function delete(int $id): void
    {
        $pdo = self::_getConnection();
        $pdo->prepare("DELETE FROM task_tags WHERE tag_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM tags WHERE id = ?")->execute([$id]);
    }


    public static function getByUser(int|string $userId): array
    {
        $pdo = self::_getConnection();
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE user_id IS NULL OR user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTagsByTaskId(int $taskId): array
    {
        $pdo = self::_getConnection();
        $stmt = $pdo->prepare(
            "SELECT tags.* FROM tags 
         JOIN task_tags ON tags.id = task_tags.tag_id 
         WHERE task_tags.task_id = ?"
        );
        $stmt->execute([$taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveTaskTags(int $taskId, array $tagIds): void
    {
        $pdo = self::_getConnection();
        $pdo->prepare("DELETE FROM task_tags WHERE task_id = ?")->execute([$taskId]);
        $stmt = $pdo->prepare("INSERT INTO task_tags (task_id, tag_id) VALUES (?, ?)");
        foreach ($tagIds as $tagId) {
            $stmt->execute([$taskId, $tagId]);
        }
    }

    public static function deleteTaskTags(int $taskId): void
    {
        $pdo = self::_getConnection();
        $pdo->prepare("DELETE FROM task_tags WHERE task_id = ?")->execute([$taskId]);
    }

    public static function getTaskIdsByTagIds(array $tagIds): array
    {
        $pdo = self::_getConnection();
        $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
        $stmt = $pdo->prepare("SELECT DISTINCT task_id FROM task_tags WHERE tag_id IN ($placeholders)");
        $stmt->execute($tagIds);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'task_id');
    }
}
