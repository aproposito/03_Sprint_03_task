<?php
class UserMysql
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

    public function findByUsername(string $username): ?array
    {
        $pdo = self::_getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public function findById(string $id): ?array
    {
        $pdo = self::_getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
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
        if (empty($username) || empty($password)) {
            return false;
        }
        if (strlen($password) < 8) {
            throw new Exception("Password too short");
        }
        $username = trim($username);
        if ($username === "" || strlen($username) > 20) {
            return false;
        }
        if ($this->findByUsername($username) !== null) {
            return false;
        }

        $pdo = self::_getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, password) VALUES (?, ?)"
        );
        $stmt->execute([
            $username,
            password_hash($password, PASSWORD_DEFAULT)
        ]);

        $newId = (int) $pdo->lastInsertId();
        return $this->findById((string) $newId);
    }
    public function editUsername(string $userId, string $newUsername): array|false
    {
        $newUsername = trim($newUsername);
        if ($newUsername === "" || strlen($newUsername) > 20) {
            return false;
        }

        $existing = $this->findByUsername($newUsername);
        if ($existing !== null && $existing['id'] !== $userId) {
            return false;
        }

        $pdo = self::_getConnection();
        $pdo->prepare("UPDATE users SET username = ? WHERE id = ?")
            ->execute([$newUsername, $userId]);

        return $this->findById($userId);
    }
    public function editPassword(string $userId, string $newPassword): array|false
    {
        if (empty($newPassword) || strlen($newPassword) < 8) {
            return false;
        }

        $pdo = self::_getConnection();
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
            ->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);

        return $this->findById($userId);
    }
    public function delete(string $userId): bool
{
    $pdo = self::_getConnection();
    
    // 1. Borrar task_tags de las tasks del usuario
    $pdo->prepare("DELETE task_tags FROM task_tags 
                   JOIN tasks ON task_tags.task_id = tasks.id 
                   WHERE tasks.user_id = ?")->execute([$userId]);
    
    // 2. Borrar tasks del usuario
    $pdo->prepare("DELETE FROM tasks WHERE user_id = ?")->execute([$userId]);
    
    // 3. Borrar task_tags de las tags privadas del usuario
    $pdo->prepare("DELETE task_tags FROM task_tags 
                   JOIN tags ON task_tags.tag_id = tags.id 
                   WHERE tags.user_id = ?")->execute([$userId]);
    
    // 4. Borrar tags privadas del usuario
    $pdo->prepare("DELETE FROM tags WHERE user_id = ?")->execute([$userId]);
    
    // 5. Borrar el usuario
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    
    return true;
}
}
