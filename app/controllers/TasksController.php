<?php
declare(strict_types=1);

class TasksController extends ApplicationController
{
    public function indexAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TaskMysql' : 'Task';
        $tasks = $modelClass::getByUser($_SESSION['user']['id']);
        $search = $this->_getParam('search');
        $status = $this->_getParam('status');

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
        $this->view->tasks = array_values($tasks);
    }

    public function showAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TaskMysql' : 'Task';
        $id = (int) $this->_getParam('id');
        $task = $modelClass::getById($id);
        $this->view->task = $task;
    }

    public function createAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TaskMysql' : 'Task';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = $this->_getAllParams();
            $task['user_id'] = $_SESSION['user']['id'] ?? 1;
            $modelClass::create($task);
            header('Location: /dashboard');
            exit;
        }
    }

    public function deleteAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TaskMysql' : 'Task';
        $id = (int) $this->_getParam('id');
        $modelClass::destroy($id);
        header('Location: /dashboard');
        exit;
    }

    public function editAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TaskMysql' : 'Task';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = $this->_getAllParams();
            $task['id'] = (int) $task['id'];
            $modelClass::update($task);
            header('Location: /dashboard');
            exit;
        } else {
            $id = (int) $this->_getParam('id');
            $task = $modelClass::getById($id);
            $this->view->task = $task;
        }
    }

    public function updateStatusAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TaskMysql' : 'Task';
        $id = (int) $this->_getParam('id');
        $status = $this->_getParam('status');
        $modelClass::updateStatus($id, $status);
        header('Location: /dashboard');
        exit;
    }
}