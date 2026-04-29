<?php

declare(strict_types=1);

class TagsController extends ApplicationController
{

    public function beforeFilters()
    {
        if (empty($_SESSION['user'])) {
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }
    }

    public function indexAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TagMysql' : 'Tag';
        $this->view->tags = $modelClass::getByUser($_SESSION['user']['id']);
    }

    public function createAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TagMysql' : 'Tag'; 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = [
                'name' => $_POST['name'],
                'color' => $_POST['color'],
                'icon' => $_POST['icon'],
                'user_id' => $_SESSION['user']['id']
            ];

            $modelClass::save($tag);
            header('Location: ' . $this->_baseUrl() . '/tags');
            exit;
        }
    }

    public function editAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TagMysql' : 'Tag';
        $id = (int) $this->_getParam('id');
        $tag = $modelClass::fetchOne($id);

        if (!$tag || ($tag['user_id'] !== $_SESSION['user']['id'])) {
            header('Location: ' . $this->_baseUrl() . '/tags');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = [
                'id'      => $id,
                'name'    => $_POST['name'],
                'color'   => $_POST['color'],
                'icon'    => $_POST['icon'],
                'user_id' => $_SESSION['user']['id']
            ];

            $modelClass::update($tag);
            header('Location: ' . $this->_baseUrl() . '/tags');
            exit;
        }
        $this->view->tag = $tag;
    }

    public function deleteAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TagMysql' : 'Tag';
        $id = (int) $this->_getParam('id');
        $tag = $modelClass::fetchOne($id);

        if ($tag && $tag['user_id'] === $_SESSION['user']['id']) {
            $modelClass::delete($id);
        }
        header('Location: ' . $this->_baseUrl() . '/tags');
        exit;
    }

    public function quickCreateAction()
    {
        $modelClass = PERSISTENCE === 'mysql' ? 'TagMysql' : 'Tag';
        $returnTo = $this->_getParam('return_to', 'tags');
        $taskId = $this->_getParam('task_id');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = [
                'name' => $_POST['name'],
                'color' => $_POST['color'],
                'icon' => $_POST['icon'],
                'user_id' => $_SESSION['user']['id']
            ];
            $modelClass::save($tag);

            if ($returnTo === 'task_create') {
                header('Location: ' . $this->_baseUrl() . '/tasks/create');
            } elseif ($returnTo === 'task_edit' && $taskId) {
                header('Location: ' . $this->_baseUrl() . '/tasks/edit/' . $taskId);
            } else {
                header('Location: ' . $this->_baseUrl() . '/tags');
            } exit;
        }
        $this->view->returnTo = $returnTo;
        $this->view->taskId = $taskId;
    }
}