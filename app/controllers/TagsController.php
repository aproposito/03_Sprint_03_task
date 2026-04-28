<?php

declare(strict_types=1);

class TagsController extends ApplicationController
{

    public function indexAction()
    {
        $this->view->tags = Tag::getByUser($_SESSION['user']['id']);
    }

    public function createAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = [
                'name' => $_POST['name'],
                'color' => $_POST['color'],
                'icon' => $_POST['icon'],
                'user_id' => $_SESSION['user']['id']
            ];

            $model = new Tag();
            $model->save($tag);
            header('Location: ' . $this->_baseUrl() . '/tags');
            exit;
        }
    }

    public function editAction()
    {
        $id = (int) $this->_getParam('id');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = [
                'id'      => $id,
                'name'    => $_POST['name'],
                'color'   => $_POST['color'],
                'icon'    => $_POST['icon'],
                'user_id' => $_SESSION['user']['id']
            ];

            $model = new Tag();
            $model->update($tag);
            header('Location: ' . $this->_baseUrl() . '/tags');
            exit; 
        } else {
            $model = new Tag();
            $this->view->tag = $model->fetchOne($id);
        }
    }

    public function deleteAction()
    {
        $id = (int) $this->_getParam('id');
        $model = new Tag();
        $model->delete($id);
        header('Location: ' . $this->_baseUrl() . '/tags');
        exit;
    }
}
