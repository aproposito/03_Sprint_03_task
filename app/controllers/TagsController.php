<?php

declare(strict_types=1);

class TagsController extends ApplicationController
{

    private string $modelClass;

    public function beforeFilters()
    {
        if (empty($_SESSION['user'])) {
            header('Location: ' . $this->_baseUrl() . '/user/login');
            exit;
        }

        $this->modelClass = PERSISTENCE === 'mysql' ? 'TagMysql' : 'Tag';
    }

    public function indexAction()
    {
        $this->view->tags = $this->modelClass::getByUser($_SESSION['user']['id']);
    }

    public function createAction()
    {
        if ($this->getRequest()->isPost()) {
            $tag = [
                'name' => $this->_getParam('name'),
                'color' => $this->_getParam('color'),
                'icon' => $this->_getParam('icon'),
                'user_id' => $_SESSION['user']['id']
            ];

            $this->modelClass::save($tag);
            header('Location: ' . $this->_baseUrl() . '/tags');
            exit;
        }
    }

    public function editAction()
    {
        $id = (int) $this->_getParam('id');
        $tag = $this->modelClass::fetchOne($id);

        if (!$tag || ($tag['user_id'] !== $_SESSION['user']['id'])) {
            header('Location: ' . $this->_baseUrl() . '/tags');
            exit;
        }

        if ($this->getRequest()->isPost()) {
            $this->modelClass::update([
                'id' => $id,
                'name' => $this->_getParam('name'),
                'color' => $this->_getParam('color'),
                'icon' => $this->_getParam('icon'),
                'user_id' => $_SESSION['user']['id']
            ]);

            header('Location: ' . $this->_baseUrl() . '/tags');
            exit;
        }
        $this->view->tag = $tag;
    }

    public function deleteAction()
    {
        $id = (int) $this->_getParam('id');
        $tag = $this->modelClass::fetchOne($id);

        if ($tag && $tag['user_id'] === $_SESSION['user']['id']) {
            $this->modelClass::delete($id);
        }
        header('Location: ' . $this->_baseUrl() . '/tags');
        exit;
    }

    public function quickCreateAction()
    {
        $returnTo = $this->_getParam('return_to', 'tags');
        $taskId = $this->_getParam('task_id');

        if ($this->getRequest()->isPost()) {
            $this->modelClass::save([
                'name' => $this->_getParam('name'),
                'color' => $this->_getParam('color'),
                'icon' => $this->_getParam('icon'),
                'user_id' => $_SESSION['user']['id']
            ]);

            if ($returnTo === 'task_create') {
                header('Location: ' . $this->_baseUrl() . '/tasks/create');
            } elseif ($returnTo === 'task_edit' && $taskId) {
                header('Location: ' . $this->_baseUrl() . '/tasks/edit/' . $taskId);
            } else {
                header('Location: ' . $this->_baseUrl() . '/tags');
            }
            exit;
        }

        $this->view->returnTo = $returnTo;
        $this->view->taskId = $taskId;
    }
}
