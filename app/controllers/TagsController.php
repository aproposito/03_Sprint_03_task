<?php

declare(strict_types=1);

class TagsController extends ApplicationController
{

    public function indexAction()
    {
        $this->view->tags = Tag::getByUser($_SESSION['user_id']);
    }

    public function createAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = [
                'name' => $_POST['name'],
                'color' => $_POST['color'],
                'icon' => $_POST['icon'],
                'user_id' => $_SESSION['user_id']
            ];

            Tag::save($tag);
            header('Location: /tags');
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
                'user_id' => $_SESSION['user_id']
            ];

            Tag::update($tag);
            header('Location: /tags');
            exit;
        }
    }

    public function deleteAction()
    {
        $id = (int) $this->_getParam('id');
        Tag::delete($id);
        header('Location: /tags');
        exit;
    }
}
