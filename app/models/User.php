<?php
class User {
    protected $_filePath;

    public function __construct() {
        $this->_filePath = ROOT_PATH . "/data/users.json";
        if (!file_exists($this->_filePath)) {
            file_put_contents($this->_filePath,"[]");
        }
    }

    public function getAllUsers() {
        $jsonString = file_get_contents($this->_filePath);
        $users = json_decode($jsonString,true);
        if($users === null) {
            return [];
        }
        return $users;
    }

    private function saveAllUsers($users) {
        $jsonString = json_encode($users, JSON_PRETTY_PRINT);
        file_put_contents($this->_filePath, $jsonString);
    }

    public function findByUsername($username) {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user["username"] === $username) {
                return $user;
            }
        }
        return null;
    }

    public function create($username, $password) {
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
        $existingUser = $this->findByUsername ($username);
        if($existingUser !== null) {
            return false;
        }
        $newUser = [
        "id" => uniqid(),
        "username" => $username,
        "password" => password_hash($password, PASSWORD_DEFAULT)
        ];
        $users = $this->getAllUsers();
        $users[] = $newUser;
        $this->saveAllUsers($users);
        return $newUser;
    }
    
    public function checkPassword($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }
        $user = $this->findByUsername($username);
        if($user === null) {
            return false;
        }
        $passwordIsCorrect = password_verify($password,$user["password"]);
        if ($passwordIsCorrect) {
            return $user;
        }
        return false;
    }

    public function findById($id) {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user["id"] === $id) {
                return $user;
            }
        }
        return null;
    }

    public function editUsername($userId, $newUsername) {
        $newUsername = trim($newUsername);
        if ($newUsername === "" || strlen($newUsername) > 20) {
            return false;
        }

        $existing = $this->findByUsername($newUsername);
        if ($existing !== null && $existing["id"] !== $userId) {
            return false;
        }

        $users = $this->getAllUsers();
        foreach ($users as &$user) {
            if ($user["id"] === $userId) {
                $user["username"] = $newUsername;
                $this->saveAllUsers($users);
                return $user;
            }
        }
        return false;
    }

    public function editPassword($userId, $newPassword) {
        if (empty($newPassword) || strlen($newPassword) < 8) {
            return false;
        }

        $users = $this->getAllUsers();
        foreach ($users as &$user) {
            if ($user["id"] === $userId) {
                $user["password"] = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->saveAllUsers($users);
                return $user;
            }
        }
        return false;
    }

    public function delete($userId) {
        $users = $this->getAllUsers();
        foreach ($users as $index => $user) {
            if ($user["id"] === $userId) {
                unset($users[$index]);
                $this->saveAllUsers(array_values($users));
                return true;
            }
        }
        return false;
    }
}
    ?>