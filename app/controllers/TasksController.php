<?php

declare(strict_types=1);

class TasksController extends ApplicationController
{

    public function indexAction()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }

        $tasks = Task::getByUser($_SESSION['user_id']);
        $search = $this->_getParam('search');
        $status = $this->_getParam('status');
        $tagId = isset($_GET['tags']) && $_GET['tags'] !== '' ? (int) $_GET['tags'] : null;
    

        if (!empty($search)) {
            $tasks = array_filter($tasks, function ($task) use ($search) {
                return str_contains($task['name'], $search);
            });
        }

        if (!empty($status)) {
            $tasks = array_filter($tasks, function ($task) use ($status) {
                return $task['status'] === $status;
            });
        }

        $this->view->tasks = $tasks;
    }
    public function showAction()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }

        $id = (int) $this->_getParam('id');
        $task = Task::getById($id, $_SESSION['user_id']);
        $this->view->task = $task;
    }
    public function createAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . $this->_baseUrl() . '/user/login');
                exit;
            }

            $task = $this->_getAllParams();
            $task['user_id'] = $_SESSION['user_id'];
            Task::create($task);
            header('Location: ' . $this->_baseUrl() . '/dashboard');
            exit;
        }

        $this->view->availableTags = Tag::getByUser($_SESSION["user"]["id"]);
    }
    public function deleteAction()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }

        $id = (int) $this->_getParam('id');
        Task::destroy($id, $_SESSION['user_id']);
        header('Location: ' . $this->_baseUrl() . '/dashboard');
        exit;
    }
    public function editAction()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tagIds = isset($_POST['tag_ids']) ? array_map('intval', $_POST['tag_ids']) : [];

            $task = $this->_getAllParams();
            $task['id'] = (int) $task['id'];
            $task['user_id'] = $_SESSION['user_id'];
            Task::update($task, $_SESSION['user_id']);
            header('Location: ' . $this->_baseUrl() . '/dashboard');
            exit;
        } else {
            $id = (int) $this->_getParam('id');
            $task = Task::getById($id, $_SESSION['user_id']);
            $this->view->task = $task;
            $this->view->availableTags = Tag::getByUser($_SESSION["user"]["id"]);
            $this->view->selectedTagIds = array_column(Tag::getTagsByTaskId($id), 'id');
        }
    }
    public function updateStatusAction()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }

        $id = (int) $this->_getParam('id');
        $status = $this->_getParam('status');
        Task::updateStatus($id, $status, $_SESSION['user_id']);
        header('Location: ' . $this->_baseUrl() . '/dashboard');
        exit;
    }
}
