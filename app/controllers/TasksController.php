<?php

declare(strict_types=1);

class TasksController extends ApplicationController
{

    public function indexAction()
    {

        $tasks = Task::getAll();
        $search = $this->_getParam('search');
        $status = $this->_getParam('status');
        $tagId = isset($_GET['tags']) && $_GET['tags'] !== '' ? (int) $_GET['tags'] : null;
    

        if (!empty($search)) {
            $tasks = array_filter($tasks, function ($task) use ($search) {
                return str_contains($task['name'], $search);
            });
        }

        // if (!empty($tagIds)) {
        //     $matchingTaskIds = Tag::getTaskIdsByTagIds($tagIds);
        //     $tasks = array_filter($tasks, fn($t) => in_array($t['id'], $matchingTaskIds));
        // }

        if ($tagId) {
            $matchingTaskIds = Tag::getTaskIdsByTagIds([$tagId]);
            $tasks = array_filter($tasks, fn($t) => in_array($t['id'], $matchingTaskIds));
        }

        foreach ($tasks as &$task) {
            $task['tags'] = Tag::getTagsByTaskId($task['id']);
        }

        $this->view->tasks = array_values($tasks);
        $this->view->allTags = Tag::getByUser($_SESSION["user"]["id"]);
        $this->view->selectedTagIds = $tagId ? [$tagId] : [];

        // $this->view->tasks = $tasks;
        }


    public function showAction()
    {

        $id = (int) $this->_getParam('id');
        $task = Task::getById($id);
        $this->view->task = $task;
    }
    public function createAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tagIds = isset($_POST['tag_ids']) ? array_map('intval', $_POST['tag_ids']) : [];
            
        $task = $this->_getAllParams();
        unset($task['tag_ids']);
        $newId = Task::create($task);
        Tag::saveTaskTags($newId, $tagIds);
            // Task::create($task);
        header('Location: ' . $this->_baseUrl() . '/dashboard');
        exit;
        }

        $this->view->availableTags = Tag::getByUser($_SESSION["user"]["id"]);
    }
    public function deleteAction()
    {
        $id = (int) $this->_getParam('id');
        Tag::deleteTaskTags($id);
        Task::destroy($id);
        header('Location: ' . $this->_baseUrl() . '/dashboard');
        exit;
    }
    public function editAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tagIds = isset($_POST['tag_ids']) ? array_map('intval', $_POST['tag_ids']) : [];

            $task = $this->_getAllParams();
            $task['id'] = (int) $task['id'];
            unset($task['tag_ids']);
            Task::update($task);
            Tag::saveTaskTags($task['id'], $tagIds);
            header('Location: ' . $this->_baseUrl() . '/dashboard');
            exit;
        } else {
            $id = (int) $this->_getParam('id');
            $task = Task::getById($id);
            $this->view->task = $task;
            $this->view->availableTags = Tag::getByUser($_SESSION["user"]["id"]);
            $this->view->selectedTagIds = array_column(Tag::getTagsByTaskId($id), 'id');
        }
    }
    public function updateStatusAction()
    {
        $id = (int) $this->_getParam('id');
        $status = $this->_getParam('status');
        Task::updateStatus($id, $status);
        header('Location: ' . $this->_baseUrl() . '/dashboard');
        exit;
    }
}
