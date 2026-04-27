<?php
class UserController extends ApplicationController {
    public function loginAction () {
        $userModel = new User();
        if ($this->getRequest()->isPost()) {
            $username = $this->_getParam("username");
            $password = $this->_getParam("password");
            $user = $userModel->checkPassword($username, $password);
            if ($user !== false) {
                $_SESSION["user"] = $user;
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
}
?>