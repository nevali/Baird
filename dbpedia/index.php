<?php

if(isset($_REQUEST['uri']))
{
	$uri = $_REQUEST['uri'];
}
else
{
	$uri = null;
}

$ns = 'http://dbpedia.org/resource/';

if(strncmp($ns, $uri, strlen($ns)))
{
	exit(1);
}
header('Location: http://en.wikipedia.org/wiki/' . substr($uri, strlen($ns)));
