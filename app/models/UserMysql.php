<?php
class UserMysql
{
    private PDO $pdo;

    public function __construct()
    {
        $settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
        $this->pdo = new PDO(
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

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function checkPassword(string $username, string $password): array|false
    {
        $user = $this->findByUsername($username);
        if ($user === null) {
            return false;
        }
        return password_verify($password, $user['password']) ? $user : false;
    }

    public function create(string $username, string $password): array|false
    {
        if ($this->findByUsername($username) !== null) {
            return false;
        }
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        return $this->findById((string) $this->pdo->lastInsertId());
    }

    public function editUsername(string $userId, string $newUsername): array|false
    {
        $existing = $this->findByUsername($newUsername);
        if ($existing !== null && $existing['id'] !== $userId) {
            return false;
        }
        $this->pdo->prepare("UPDATE users SET username = ? WHERE id = ?")
            ->execute([$newUsername, $userId]);
        return $this->findById($userId);
    }

    public function editPassword(string $userId, string $newPassword): array|false
    {
        $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
            ->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
        return $this->findById($userId);
    }

    public function delete(string $userId): bool
    {
        $this->pdo->prepare("DELETE task_tags FROM task_tags JOIN tasks ON task_tags.task_id = tasks.id WHERE tasks.user_id = ?")->execute([$userId]);
        $this->pdo->prepare("DELETE FROM tasks WHERE user_id = ?")->execute([$userId]);
        $this->pdo->prepare("DELETE task_tags FROM task_tags JOIN tags ON task_tags.tag_id = tags.id WHERE tags.user_id = ?")->execute([$userId]);
        $this->pdo->prepare("DELETE FROM tags WHERE user_id = ?")->execute([$userId]);
        $this->pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
        return true;
    }
}
