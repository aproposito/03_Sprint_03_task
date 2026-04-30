<?php
class UserController extends ApplicationController
{
    private function _getUserModel(): User
    {
        return PERSISTENCE === 'mysql' ? new UserMysql() : new User();
    }

    private function _requireLogin(): string
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }
        return (string) $userId;
    }

    public function loginAction(): void
    {
        if ($this->getRequest()->isPost()) {
            $username = (string) $this->_getParam('username');
            $password = (string) $this->_getParam('password');
            $user     = $this->_getUserModel()->checkPassword($username, $password);

            if ($user !== false) {
                $_SESSION['user']    = $user;
                $_SESSION['user_id'] = $user['id'];
                header('Location: ' . $this->_baseUrl() . '/dashboard');
                exit;
            }
            $this->view->error = 'Wrong username or password. Please try again.';
        }
    }

    public function registerAction(): void
    {
        if ($this->getRequest()->isPost()) {
            $username = (string) $this->_getParam('username');
            $password = (string) $this->_getParam('password');

            $error = UserValidator::validateUsername($username)
                  ?? UserValidator::validatePassword($password);

            if ($error !== null) {
                $this->view->error = $error;
                return;
            }

            $newUser = $this->_getUserModel()->create($username, $password);
            if ($newUser !== false) {
                $_SESSION['user']    = $newUser;
                $_SESSION['user_id'] = $newUser['id'];
                header('Location: ' . $this->_baseUrl() . '/dashboard');
                exit;
            }
            $this->view->error = 'That username is already taken. Please choose another.';
        }
    }

    public function logoutAction(): void
    {
        session_destroy();
        header('Location: ' . $this->_baseUrl() . '/user/login');
        exit;
    }

    public function profileAction(): void
    {
        $userId = $this->_requireLogin();
        $user   = $this->_getUserModel()->findById($userId);

        if (!$user) {
            session_destroy();
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }

        $this->view->user         = $user;
        $this->view->message      = $_SESSION['user_message'] ?? null;
        $this->view->message_type = $_SESSION['user_message_type'] ?? null;
        unset($_SESSION['user_message'], $_SESSION['user_message_type']);
    }

    public function updateUsernameAction(): void
    {
        $userId = $this->_requireLogin();
        if (!$this->getRequest()->isPost()) {
            header('Location: ' . $this->_baseUrl() . '/user/profile');
            exit;
        }

        $newUsername = trim((string) $this->_getParam('username'));
        $error       = UserValidator::validateUsername($newUsername);

        if ($error !== null) {
            $_SESSION['user_message']      = $error;
            $_SESSION['user_message_type'] = 'error';
        } else {
            $updatedUser = $this->_getUserModel()->editUsername($userId, $newUsername);
            if ($updatedUser !== false) {
                $_SESSION['user']              = $updatedUser;
                $_SESSION['user_message']      = 'Username updated successfully.';
                $_SESSION['user_message_type'] = 'success';
            } else {
                $_SESSION['user_message']      = 'That username is already taken.';
                $_SESSION['user_message_type'] = 'error';
            }
        }

        header('Location: ' . $this->_baseUrl() . '/user/profile');
        exit;
    }

    public function updatePasswordAction(): void
    {
        $userId = $this->_requireLogin();
        if (!$this->getRequest()->isPost()) {
            header('Location: ' . $this->_baseUrl() . '/user/profile');
            exit;
        }

        $newPassword = (string) $this->_getParam('password');
        $error       = UserValidator::validatePassword($newPassword);

        if ($error !== null) {
            $_SESSION['user_message']      = $error;
            $_SESSION['user_message_type'] = 'error';
        } elseif ($this->_getUserModel()->editPassword($userId, $newPassword) !== false) {
            $_SESSION['user_message']      = 'Password updated successfully.';
            $_SESSION['user_message_type'] = 'success';
        } else {
            $_SESSION['user_message']      = 'Unable to update password. Please try again.';
            $_SESSION['user_message_type'] = 'error';
        }

        header('Location: ' . $this->_baseUrl() . '/user/profile');
        exit;
    }

    public function deleteAction(): void
    {
        $userId = $this->_requireLogin();
        if (!$this->getRequest()->isPost()) {
            header('Location: ' . $this->_baseUrl() . '/user/profile');
            exit;
        }

        if ($this->_getUserModel()->delete($userId)) {
            session_destroy();
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }

        $_SESSION['user_message']      = 'Unable to delete account. Please try again.';
        $_SESSION['user_message_type'] = 'error';
        header('Location: ' . $this->_baseUrl() . '/user/profile');
        exit;
    }
}
