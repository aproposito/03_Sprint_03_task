<?php
class User
{
    protected string $_filePath;

    public function __construct()
    {
        $this->_filePath = ROOT_PATH . "/data/users.json";
        if (!file_exists($this->_filePath)) {
            file_put_contents($this->_filePath, "[]");
        }
    }

    private function getAllUsers(): array
    {
        $users = json_decode(file_get_contents($this->_filePath), true);
        return $users ?? [];
    }

    private function saveAllUsers(array $users): void
    {
        file_put_contents($this->_filePath, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function findByUsername(string $username): ?array
    {
        foreach ($this->getAllUsers() as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }

    public function findById(string $id): ?array
    {
        foreach ($this->getAllUsers() as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }
        return null;
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
        $newUser = [
            'id'       => uniqid(),
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];
        $users   = $this->getAllUsers();
        $users[] = $newUser;
        $this->saveAllUsers($users);
        return $newUser;
    }

    public function editUsername(string $userId, string $newUsername): array|false
    {
        $existing = $this->findByUsername($newUsername);
        if ($existing !== null && $existing['id'] !== $userId) {
            return false;
        }
        $users = $this->getAllUsers();
        foreach ($users as &$user) {
            if ($user['id'] === $userId) {
                $user['username'] = $newUsername;
                $this->saveAllUsers($users);
                return $user;
            }
        }
        return false;
    }

    public function editPassword(string $userId, string $newPassword): array|false
    {
        $users = $this->getAllUsers();
        foreach ($users as &$user) {
            if ($user['id'] === $userId) {
                $user['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->saveAllUsers($users);
                return $user;
            }
        }
        return false;
    }

    public function delete(string $userId): bool
    {
        $users = $this->getAllUsers();
        foreach ($users as $index => $user) {
            if ($user['id'] === $userId) {
                unset($users[$index]);
                $this->saveAllUsers(array_values($users));
                return true;
            }
        }
        return false;
    }
}
