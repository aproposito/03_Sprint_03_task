<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller 
{
    public function init()
    {
        parent::init();
        $this->view->baseUrl = $this->_baseUrl();
    }
}

