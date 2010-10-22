<?php

define('MODULES_ROOT', dirname(__FILE__) . '/../app/');
define('CACHE_DIR', dirname(__FILE__) . '/../cache/');
define('CACHE_TIME', 3600);

require_once(dirname(__FILE__) . '/../platform/lib/common.php');

uses('rdf', 'curl');

$format = 'json';
$location = null;
if(!isset($_REQUEST['uri']))
{
	require_once(dirname(__FILE__) . '/form.phtml');
	exit();
}
$location = $_REQUEST['uri'];
$reverse = !empty($_REQUEST['reverse']);
$flat = !empty($_REQUEST['flat']);
if(isset($_REQUEST['format']))
{
	$format = $_REQUEST['format'];
}
if(!empty($_REQUEST['mode']))
{
	if($_REQUEST['mode'] == 'reverse')
	{
		$reverse = true;
		$flat = false;
	}
	else if($_REQUEST['mode'] == 'flat')
	{
		$reverse = false;
		$flat = true;
	}
}
$doc = RDF::documentFromURL($location);
$subjects = array();
if(is_object($doc))
{
	$graphs = $doc->graphs;
	foreach($graphs as $graph)
	{		
		foreach($graph as $k => $v)
		{
			delve_property($subjects, $k, $v, $reverse, $flat);
		}
	}
}

if($format == 'html')
{
	require_once(dirname(__FILE__) . '/form.phtml');
	exit();
}

header('Content-type: text/javascript');
echo json_encode($subjects);

function delve_property(&$subjects, $prop, $values, $reverse = false, $flat = false)
{
	foreach($values as $value)
	{
		if(!is_object($value))
		{
			continue;
		}
		if($value instanceof RDFURI)
		{
			$uri = $value->value;
			if($flat)
			{
				if(!in_array($uri, $subjects))
				{
					$subjects[] = $uri;
				}
			}
			else if($reverse)
			{
				if(!isset($subjects[$uri]) || !in_array($prop, $subjects[$uri]))
				{
					$subjects[$uri][] = $prop;
				}
			}
			else
			{
				if(!isset($subjects[$prop]) || !in_array($uri, $subjects[$prop]))
				{
					$subjects[$prop][] = $uri;
				}
			}
		}
		else if($value instanceof RDFGraph)
		{
			foreach($value as $k => $v)
			{
				delve_property($subjects, $k, $v, $reverse, $flat);
			}
		}
	}
}
