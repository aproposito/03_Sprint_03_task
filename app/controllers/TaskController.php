<?php
declare(strict_types=1);

class TasksController extends ApplicationController {

	public function indexAction(){
  
    $tasks = Task::getAll();
    $this->view->tasks = $tasks;
}
    
    public function showAction(){

    $id = $this->_getParam('id');
    $task = Task::getById($id);
    $this->view->task = $task;
  
}
    }
    public function createAction(){}
    public function deleteAction(){}
    public function updateAction(){}
    public function updateStatusAction(){}

	
}