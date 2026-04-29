<?php
class UserController extends ApplicationController {
   public function loginAction() {
    $userModel = new User();
    if ($this->getRequest()->isPost()) {
        $username = $this->_getParam("username");
        $password = $this->_getParam("password");
        $user = $userModel->checkPassword($username, $password);
        if ($user !== false) {
            $_SESSION["user"] = $user;
            $_SESSION["user_id"] = $user["id"];
            header("Location: " . $this->_baseUrl() . "/dashboard");
            exit;
        } else {
            $this->view->error = "Wrong username or password. Please try again.";
        }
    }
}


    public function registerAction() {
        $userModel = new User();
        if ($this->getRequest()->isPost()) {
            $username = $this->_getParam("username");
            $password = $this->_getParam("password");
            try {
                $newUser = $userModel->create($username, $password);

                if ($newUser !== false) {
                    $_SESSION["user"] = $newUser;
                    $_SESSION["user_id"] = $newUser["id"];
                    header("Location: " . $this->_baseUrl() . "/user/login");
                    exit;
                } else {
                    $this->view->error = "That username is already taken. Please choose another.";
                }
            }
            catch (Exception $e) {
                $this->view->error = $e->getMessage();
            }
        }
    } 
    public function logoutAction() {
        session_destroy();
        header("Location: " . $this->_baseUrl() . "/user/login");
        exit;
    }
    public function profileAction() {
        $userModel = new User();
        $userId = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
        if (!$userId) {
            header("Location: " . $this->_baseUrl() . "/user/login");
            exit;
        }

        $user = $userModel->findById($userId);
        if (!$user) {
            session_destroy();
            header("Location: " . $this->_baseUrl() . "/user/login");
            exit;
        }

        $this->view->user = $user;
        $this->view->message = isset($_SESSION["user_message"]) ? $_SESSION["user_message"] : null;
        $this->view->message_type = isset($_SESSION["user_message_type"]) ? $_SESSION["user_message_type"] : null;
        unset($_SESSION["user_message"], $_SESSION["user_message_type"]);
    }
    public function updateUsernameAction() {
        $userModel = new User();
        $userId = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
        if (!$userId || !$this->getRequest()->isPost()) {
            header("Location: " . $this->_baseUrl() . "/user/profile");
            exit;
        }

        $newUsername = trim($this->_getParam("username"));
        if ($userModel->editUsername($userId, $newUsername) !== false) {
            $updatedUser = $userModel->findById($userId);
            $_SESSION["user"] = $updatedUser;
            $_SESSION["user_message"] = "Username updated successfully.";
            $_SESSION["user_message_type"] = "success";
        } else {
            $_SESSION["user_message"] = "That username is invalid or already taken.";
            $_SESSION["user_message_type"] = "error";
        }

        header("Location: " . $this->_baseUrl() . "/user/profile");
        exit;
    }
    public function updatePasswordAction() {
        $userModel = new User();
        $userId = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
        if (!$userId || !$this->getRequest()->isPost()) {
            header("Location: " . $this->_baseUrl() . "/user/profile");
            exit;
        }

        $newPassword = $this->_getParam("password");
        if ($userModel->editPassword($userId, $newPassword) !== false) {
            $_SESSION["user_message"] = "Password updated successfully.";
            $_SESSION["user_message_type"] = "success";
        } else {
            $_SESSION["user_message"] = "Password must be at least 8 characters long.";
            $_SESSION["user_message_type"] = "error";
        }

        header("Location: " . $this->_baseUrl() . "/user/profile");
        exit;
    }
    public function deleteAction() {
        $userModel = new User();
        $userId = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
        if (!$userId || !$this->getRequest()->isPost()) {
            header("Location: " . $this->_baseUrl() . "/user/profile");
            exit;
        }

        if ($userModel->delete($userId)) {
            session_destroy();
            header("Location: " . $this->_baseUrl() . "/user/login");
            exit;
        }

        $_SESSION["user_message"] = "Unable to delete account. Please try again.";
        $_SESSION["user_message_type"] = "error";
        header("Location: " . $this->_baseUrl() . "/user/profile");
        exit;
    }
}
?>