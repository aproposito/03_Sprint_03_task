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

        if ($tagId) {
            $matchingTaskIds = Tag::getTaskIdsByTagIds([$tagId]);
            $tasks = array_filter($tasks, fn($t) => in_array($t['id'], $matchingTaskIds));
        }

        foreach ($tasks as &$task) {
            $task['tags'] = Tag::getTagsByTaskId($task['id']);
        }

        $this->view->tasks = array_values($tasks);
        $this->view->allTags = Tag::getByUser($_SESSION['user']['id']);
        $this->view->selectedTagIds = $tagId ? [$tagId] : [];
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
            $tagIds = isset($_POST['tag_ids']) ? array_map('intval', $_POST['tag_ids']) : [];
            $task = $this->_getAllParams();
            unset($task['tag_ids']);
            $task['user_id'] = $_SESSION['user']['id'] ?? 1;
            $newId = $modelClass::create($task);
            Tag::saveTaskTags($newId, $tagIds);
            header('Location: /dashboard');
            exit;
        }
        $this->view->availableTags = Tag::getByUser($_SESSION['user']['id']);
    }

    public function deleteAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TaskMysql' : 'Task';
        $id = (int) $this->_getParam('id');
        Tag::deleteTaskTags($id);
        $modelClass::destroy($id);
        header('Location: /dashboard');
        exit;
    }

    public function editAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TaskMysql' : 'Task';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tagIds = isset($_POST['tag_ids']) ? array_map('intval', $_POST['tag_ids']) : [];
            $task = $this->_getAllParams();
            $task['id'] = (int) $task['id'];
            $task['user_id'] = $_SESSION['user']['id'] ?? 1;
            unset($task['tag_ids']);
            $modelClass::update($task);
            Tag::saveTaskTags($task['id'], $tagIds);
            header('Location: /dashboard');
            exit;
        } else {
            $id = (int) $this->_getParam('id');
            $task = $modelClass::getById($id);
            $this->view->task = $task;
            $this->view->availableTags = Tag::getByUser($_SESSION['user']['id']);
            $this->view->selectedTagIds = array_column(Tag::getTagsByTaskId($id), 'id');
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