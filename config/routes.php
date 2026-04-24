<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
	'/test' => 'test#index',
	'/tasks' => 'tasks#index',
	'/tasks/show/:id' => 'tasks#show',
	'/tasks/create' => 'tasks#create',
	'/tasks/delete/:id' => 'tasks#delete',
	'/tasks/update/:id' => 'tasks#update',
	'/tasks/updateStatus/:id' => 'tasks#updateStatus'
);
