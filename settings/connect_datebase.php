<?php
	$mysqli = new mysqli('127.0.0.1', 'root', '', 'security');
	
	function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    $user_ip = getClientIP();

	setcookie("IP", $user_ip);

	setcookie("Datetime", date("Y-m-d H:i:s"));
?>