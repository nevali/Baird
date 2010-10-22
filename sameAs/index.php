<?php

define('MODULES_ROOT', dirname(__FILE__) . '/../app/');
define('CACHE_DIR', dirname(__FILE__) . '/../cache/');
define('CACHE_TIME', 3600);

require_once(dirname(__FILE__) . '/../platform/lib/common.php');

uses('rdf', 'curl');

if(!isset($_REQUEST['uri']))
{
	require_once(dirname(__FILE__) . '/form.phtml');
	exit();
}
$format = 'json';
if(isset($_REQUEST['format']))
{
	$format = $_REQUEST['format'];
}
$location = $_REQUEST['uri'];
$reverse = !empty($_REQUEST['reverse']);
$flat = !empty($_REQUEST['flat']);
$doc = RDF::documentFromURL($location);
$list = array(
	'sameAs' => array(),
	'exactMatch' => array(),
	'closeMatch' => array(),
	'narrowMatch' => array(),
	'broadMatch' => array(),
	);
if(is_object($doc))
{
	$primaryTopic = $doc->primaryTopic();
	if(!empty($_REQUEST['debug']))
	{
		print_r($primaryTopic);
	}
	sameAs_add_property($list['sameAs'], $primaryTopic, RDF::owl . 'sameAs');
	sameAs_add_property($list['exactMatch'], $primaryTopic, RDF::skos . 'exactMatch');
	sameAs_add_property($list['closeMatch'], $primaryTopic, RDF::skos . 'closeMatch');
	sameAs_add_property($list['narrowMatch'], $primaryTopic, RDF::skos . 'narrowMatch');
	sameAs_add_property($list['broadMatch'], $primaryTopic, RDF::skos . 'broadMatch');
}
else if(!empty($_REQUEST['debug']))
{
	echo "No document found\n";
}

$curl = new CurlCache('http://sameas.org/json?uri=' . urlencode($location));
$curl->returnTransfer = true;
$buf = $curl->exec();
if($buf !== false && strlen($buf))
{
	$buf = json_decode($buf, true);
	if(is_array($buf) && isset($buf[0]['duplicates']))
	{
		foreach($buf[0]['duplicates'] as $uri)
		{
			if(!in_array($uri, $list['sameAs']))
			{
				$list['sameAs'][] = $uri;
			}
		}
	}
}

if($format == 'html')
{
	require_once(dirname(__FILE__) .'/form.phtml');
	exit();
}
@header('Content-type: text/javascript');
echo json_encode($list);


function sameAs_add_property(&$list, $graph, $propName)
{
	if(isset($graph->{$propName}))
	{
		foreach($graph->{$propName} as $uri)
		{
			if(($uri instanceof RDFURI))
			{
				$uri = $uri->value;
				if(!in_array($uri, $list))
				{
					$list[] = $uri;
				}
			}
		}
	}
}