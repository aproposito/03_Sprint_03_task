<?php

declare(strict_types=1);

class TasksController extends ApplicationController
{

   public function indexAction(){

        $tasks = Task::getAll();
        $this->view->tasks = $tasks;
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
            $task = $this->_getAllParams();
            Task::create($task);
            header('Location: /tasks');
            exit;
        }
    }
    public function deleteAction()
    {
        $id = (int) $this->_getParam('id');
        Task::destroy($id);
        header('Location: /tasks');
        exit;
    }
    public function editAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = $this->_getAllParams();
            $task['id'] = (int) $task['id'];
            Task::update($task);
            header('Location: /tasks');
            exit;
        } else {
            $id = (int) $this->_getParam('id');
            $task = Task::getById($id);
            $this->view->task = $task;
        }
    }
    public function updateStatusAction()
    {
        $id = (int) $this->_getParam('id');
        $status = $this->_getParam('status');
        Task::updateStatus($id, $status);
        header('Location: /tasks');
        exit;
    }
}
