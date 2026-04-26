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
	'/tags' => 'tags#index',
	'/tags/create' => 'tags#create',
	'/tags/edit/:id' => 'tags#edit',
	'/tags/delete/:id' => 'tags#delete'
);
