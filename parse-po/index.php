<?php

define('MODULES_ROOT', dirname(__FILE__) . '/../app/');
define('CACHE_DIR', dirname(__FILE__) . '/../cache/');
define('CACHE_TIME', 3600);

require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(MODULES_ROOT . 'radiodns/radiodns.php');
require_once(MODULES_ROOT . 'xrd/xrd.php');
require_once(dirname(__FILE__) . '/../boxify/channel.php');
require_once(MODULES_ROOT . 'po/ontology.php');
require_once(dirname(__FILE__) . '/../discovery/getrdf.php');

uses('rdf', 'curl');

if(!isset($_REQUEST['po']))
{
	exit();
}
$location = $_REQUEST['po'];
$episode = $series = $brand = null;
$applinks = array();

$doc = RDF::documentFromURL($location);
if(is_object($doc))
{
	$episode = ProgrammesOntology::instanceFromDocument($doc);
}
if(is_object($episode) && $episode instanceof POVersion)
{
	$ver = $episode;
	$episode = null;
	if(isset($ver->episode))
	{
		$location = $ver->episode;
		$doc = RDF::documentFromURL($ver->episode);
		if(is_object($doc))
		{
			$episode = ProgrammesOntology::instanceFromDocument($doc);
		}
	}  
}	
if(!is_object($episode) || !($episode instanceof POEpisode))
{
	$episode = null;
}
if(isset($episode->series))
{
	$doc = RDF::documentFromURL($episode->series);
	if(is_object($doc))
	{
		$series = ProgrammesOntology::instanceFromDocument($doc);
	}
}
if(!is_object($series) || !($series instanceof POSeries))
{
	$series = null;
}	
if(isset($series->brand))
{
	$doc = RDF::documentFromURL($series->brand);
	if(is_object($doc))
	{
		$brand = ProgrammesOntology::instanceFromDocument($doc);
	}
}
else if(isset($episode->brand))
{
	$doc = RDF::documentFromURL($episode->brand);
	if(is_object($doc))
	{
		$brand = ProgrammesOntology::instanceFromDocument($doc);
	}
}	
if(!is_object($brand) || !($brand instanceof POBrand))
{
	$brand = null;
}   
if(isset($episode->title))
{
	$page_title = $episode->title;
}
if(isset($series->title))
{
	$page_title = $series->title . ': ' . $page_title;
}
if(isset($brand->title))
{
	$page_title = $brand->title . ' - ' . $page_title;
}
ob_start();
$subjects = rdf_subjects_url($location);
ob_end_clean();
if(!empty($_REQUEST['dump-subjects']))
{
	echo '<pre>';
	print_r($subjects);
	echo '</pre>';
	die();
}

require_once(dirname(__FILE__) . '/apps.php');

foreach($subjects as $k => $subj)
{
	if($k != 'http://purl.org/ontology/po/subject' &&
	   $k != 'http://purl.org/ontology/po/person' && 
	   $k != 'http://purl.org/ontology/po/place')
	{
		continue;
	}
	foreach($subj as $info)
	{
		if(!isset($info['label'])) continue;
		$key = $info['uri'][0];
		foreach($info['uri'] as $url)
		{
			foreach($apps as $reg)
			{
				if(!strncmp($url, $reg['namespace'], strlen($reg['namespace'])))
				{
					$ui = parse_url($url);
					$r = array('%s' => @$ui['scheme'],
							   '%h' => @$ui['host'] . (isset($ui['port']) ? ':' . $ui['port'] : ''),
							   '%p' => @$ui['path'],
							   '%b' => @basename($ui['path']),
							   '%d' => @dirname($ui['path']),
							   '%f' => @$ui['fragment'],
							   '%q' => @$ui['query'],
							   '%l' => $info['label'],
							   '%u' => $url,
						);
					$dest = str_replace(array_keys($r), array_values($r), $reg['transform']);
					$applinks[$key][] = array(
						'label' => $reg['label'],
						'topic' => $info['label'],
						'href' => $dest,
						'action' => str_replace(array_keys($r), array_values($r), $reg['action']));					
				}
			}
		}
	}
}

$event = array();

if($episode)
{
	$event['status'] = 200;
	$event['statusText'] = 'OK';
	$event['title'] = $page_title;
	$event['url'] = $location;
	$event['episode'] = array(
		'pid' => $episode->pid,
		'title' => $episode->title,
		'depiction' => strval($episode->depiction),
		'shortSynopsis' => $episode->shortSynopsis,
		'mediumSynopsis' => $episode->mediumSynopsis,
		'longSynopsis' => $episode->longSynopsis,
		'microsite' => $episode->microsite,
		);
	if($series)
	{
		$event['series'] = array(
			'pid' => $series->pid,
			'title' => $series->title,
			'depiction' => strval($series->depiction),
			'microsite' => $series->microsite,
			);
	}
	if($brand)
	{
		$event['brand'] = array(
			'pid' => $brand->pid,
			'title' => $brand->title,
			'depiction' => strval($brand->depiction),
			'microsite' => $brand->microsite,
			);
	}
	$event['apps'] = $applinks;
}
else
{
	$event['status'] = 404;
	$event['statusText'] = 'Event not matched';
}
header('Content-type: text/javascript');
echo json_encode($event);