<?php

define('MODULES_ROOT', dirname(__FILE__) . '/../app/');
define('CACHE_DIR', dirname(__FILE__) . '/../cache/');
define('CACHE_TIME', 3600);

require_once(dirname(__FILE__) . '/../platform/lib/common.php');

uses('rdf', 'curl');

if(!isset($_REQUEST['uri']))
{
	exit();
}
$location = $_REQUEST['uri'];
$reverse = !empty($_REQUEST['reverse']);
$flat = !empty($_REQUEST['flat']);
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
