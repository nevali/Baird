<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('UTC');
ini_set('default_charset', 'UTF-8');

$query_string = null;
if(isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']))
{
	$query_string = '?' . $_SERVER['QUERY_STRING'];
}

function _e($str)
{
	return str_replace('&quot;', '&#39;', htmlspecialchars($str));
}

function e($str)
{
	echo _e($str);
}
