<?php
	ini_set('display_errors', 1);
	include('JSONapi.php');

	$ip = 'soul.omgcraft.fr';
	$port = 20059;
	$user = "nix";
	$pwd = "dragonball";
	$salt = 'salt';
	$api = new JSONAPI($ip, $port, $user, $pwd, $salt);

	var_dump($api->call('players.online.names'));

?>
