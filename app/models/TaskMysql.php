<?php
declare(strict_types =1);

class TaskMysql extends Model {

private static function _getConnection(): PDO
{
    $settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
    
    return new PDO(
        sprintf("%s:host=%s;dbname=%s;charset=utf8mb4",
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
    $stmt = $pdo->query("SELECT * FROM tasks");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function getByUser(int|string $userId): array
{
    $pdo = self::_getConnection();
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY start_time DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function getById(int $id): ?array
{
    $pdo = self::_getConnection();
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

public static function create(array $task): int
{
    $pdo = self::_getConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO tasks (name, description, status, start_time, end_time, user_id)
         VALUES (:name, :description, :status, NOW(), :end_time, :user_id)"
    );
    $stmt->execute([
        'name'        => $task['name'],
        'description' => $task['description'] ?? '',
        'status'      => $task['status'] ?? 'pending',
        'end_time'    => $task['end_time'] ?? null,
        'user_id'     => $task['user_id'] ?? null,
    ]);
    return (int) $pdo->lastInsertId();
}

public static function destroy(int $id): void
{
    $pdo = self::_getConnection();
    $pdo->prepare("DELETE FROM task_tags WHERE task_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM tasks WHERE id = ?")->execute([$id]);
}

public static function update(array $task): void
{
    $pdo = self::_getConnection();
    $endTime = ($task['status'] === 'completed') ? date('Y-m-d H:i:s') : ($task['end_time'] ?? null);
    $stmt = $pdo->prepare(
        "UPDATE tasks SET 
            name = :name,
            description = :description,
            status = :status,
            end_time = :end_time
         WHERE id = :id"
    );
    $stmt->execute([
        'name'        => $task['name'],
        'description' => $task['description'],
        'status'      => $task['status'],
        'end_time'    => $endTime,
        'id'          => $task['id'],
    ]);
}

public static function updateStatus(int $id, string $status): void
{
    $pdo = self::_getConnection();
    $endTime = ($status === 'completed') ? date('Y-m-d H:i:s') : null;
    $stmt = $pdo->prepare(
        "UPDATE tasks SET status = ?, end_time = ? WHERE id = ?"
    );
    $stmt->execute([$status, $endTime, $id]);
}
}