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
//tags routes
	'/tags' => 'tags#index',
	'/tags/create' => 'tags#create',
	'/tags/edit/:id' => 'tags#edit',
	'/tags/delete/:id' => 'tags#delete',
	'/tags/quick-create' => 'tags#quickCreate',

//tasks routes
	'/test' => 'test#index',
	'/dashboard' => 'tasks#index',
	'/tasks/create' => 'tasks#create',
	'/tasks/delete/:id' => 'tasks#delete',
	'/tasks/edit/:id' => 'tasks#edit',
	'/tasks/updateStatus/:id' => 'tasks#updateStatus',

//user routes
	'/user/login' => 'user#login',
	'/user/register' => 'user#register',
	'/user/logout' => 'user#logout',
	'/user/profile' => 'user#profile',
	'/user/update-username' => 'user#updateUsername',
	'/user/update-password' => 'user#updatePassword',
	'/user/delete' => 'user#delete',
);
