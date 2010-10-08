<?php

define('CACHE_DIR', dirname(__FILE__) . '/../cache/');

require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(dirname(__FILE__) . '/getrdf.php');

if(!isset($_GET['url']))
{
	die('No URL specified');
}

$url = $_GET['url'];
ob_start();
$subjects = rdf_subjects_url($url);
$errors = ob_get_clean();
if(!$subjects)
{
	echo '<pre>' . $errors . '</pre>';
	die(1);
}
echo '<pre>';
print_r($subjects);
echo '</pre>';