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

require './vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Illuminate\Database\Connection(new PDO('sqlite::memory:'));


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$query = rtrim($_POST['q'], ';');
	$query = stripslashes($query);

	$capsule->pretend(function($capsule) use ($query) {
		eval('$capsule->table("first_table")->' . $query . ';');
	});

	$result = end($capsule->getQueryLog());

	echo json_encode($result);
} else {
	require 'static/index.html';
}