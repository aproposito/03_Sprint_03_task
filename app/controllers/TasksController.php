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

        $id = $this->_getParam('id');
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
        $id = $this->_getParam('id');
        Task::destroy($id);
        header('Location: /tasks');
        exit;
    }
    public function updateAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = $this->_getAllParams();
            Task::update($task);
            header('Location: /tasks');
            exit;
        } else {
            $id = $this->_getParam('id');
            $task = Task::getById($id);
            $this->view->task = $task;
        }
    }
    public function updateStatusAction()
    {
        $id = $this->_getParam('id');
        $status = $this->_getParam('status');
        Task::updateStatus($id, $status);
        header('Location: /tasks');
        exit;
    }
}
