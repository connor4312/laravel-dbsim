<?php
/**
 * laravel-dbsim - A handy app to write expressions with the Laravel query
 * builder, and see the generated SQL. This is a personal tool - don't place it
 * where evil people can get their hands on it; there's not protection from
 * code injections.
 * 
 * @author Connor Peet <connor@peet.io>
 * @copyright 2014 Connor Peet
 * @license MIT
 */

// Boot up and autoload composer
require './vendor/autoload.php';

// Let's fake PDO if it doesn't exist, or remove PDO's constructor if it does
if (class_exists('PDO')) {
	class fakePDO extends PDO {
		public function __construct() {}
	}
	$interface = new fakePDO;
} else {
	class PDO {}
	$interface = new PDO;
}

// Set the connection to use. The PDO doesn't matter
$connection = new Illuminate\Database\Connection($interface);

// Create a connection resolver for the model, to return the above connection
class Resolver implements Illuminate\Database\ConnectionResolverInterface {
	public function connection($name = null) {
		return $this->getDefaultConnection();
	}
	public function getDefaultConnection() {
		global $connection;
		return $connection;
	}
	public function setDefaultConnection($name) {}
}

// Create a base model to use for querying
class Model extends Illuminate\Database\Eloquent\Model {}

// Tell the model to use our own resolver, instead of default from configs
Model::setConnectionResolver(new Resolver);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['q'])) {

	$query = rtrim($_POST['q'], ';');
	$query = stripslashes($query);
	$query = str_replace('DB::', '$connection->', $query);

	$connection->pretend(function($connection) use ($query) {
		eval('Model::' . $query . ';');
	});

	$log = $connection->getQueryLog();
	$result = end($log);

	echo json_encode($result);
} else {
	require 'static/index.html';
}
