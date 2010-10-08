<?php

define('MODULES_ROOT', dirname(__FILE__) . '/../app/');
define('CACHE_DIR', dirname(__FILE__) . '/../cache/');
define('CACHE_TIME', 3600);

require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(MODULES_ROOT . 'radiodns/radiodns.php');
require_once(MODULES_ROOT . 'xrd/xrd.php');
require_once(MODULES_ROOT . 'po/ontology.php');
require_once(dirname(__FILE__) . '/../boxify/channel.php');
require_once(dirname(__FILE__) . '/../boxify/dvb.php');
require_once(dirname(__FILE__) . '/../boxify/ip.php');
require_once(dirname(__FILE__) . '/../discovery/getrdf.php');

uses('rdf', 'curl');

$listing = new ChannelListing();
$xrds = new ChannelXRDS();
$sources = array();

if(!empty($_REQUEST['kind']) && $_REQUEST['kind'] == 'dvb' && !empty($_REQUEST['onid']) && !empty($_REQUEST['nid']) && !empty($_REQUEST['tsid']) && !empty($_REQUEST['sid']))
{
	$kind = 'dvb';
	$onid = trim($_REQUEST['onid']);
	$nid = trim($_REQUEST['nid']);
	$tsid = trim($_REQUEST['tsid']);
	$sid = trim($_REQUEST['sid']);
	if(isset($platform[$kind]['onid'][$onid]['nid'][$nid]['tsid'][$tsid]['sid'][$sid]))
	{
		$sources[] = array('kind' => 'dvb', 'onid' => $onid, 'nid' => $nid, 'tsid' => $tsid, 'sid' => $sid);
	}
	else
	{
		$alerts[] = 'The specified DVB network does not exist';
	}	
}
else if(!empty($_REQUEST['url']))
{
	$sources[] = array('kind' => 'ip', 'url' => $_REQUEST['url']);
}

$location = null;
while(count($sources))
{
	$source = array_shift($sources);
	switch($source['kind'])
	{
	case 'dvb':
		DVB::addChannelsFromSource($listing, $source['onid'], $source['nid'], $source['tsid'], $source['sid']);
		break;
	case 'ip':
		$location = $source['url'];
		break;
	default:
		trigger_error('Unsupported source kind ' . $source['kind'], E_USER_WARNING);
	}
}

/* For now, just pick out the first channel */
$selectedChannel = null;
$channels = array_slice($listing->channels(), 0, 1);
$listing->replaceChannels($channels);
foreach($channels as $chan)
{
	$selectedChannel = $chan;
	break;
}
if(!$selectedChannel && !strlen($location))
{
	trigger_error('No channel selected', E_USER_ERROR);
	die(1);
}

/* Fetch the XRD, if available */
if($selectedChannel)
{
	if(empty($selectedChannel->xrdFetched) && isset($selectedChannel->services['_xrd._tcp']))
	{
		$selectedChannel->xrdFetched = true;
		$xrds->mergeFrom(XRDS::xrdsFromURI($chan->services['_xrd._tcp']));
	}
	
	$xrds->locateParents();
	$xrds->matchPlatformsAgainstListing($listing);
	$listing->matchXRDS($xrds);
}
else
{
	$selectedChannel = new stdClass;
	$selectedChannel->services = array();
	$selectedChannel->xrdFetched = true;
	$selectedChannel->title = 'IP Stream';
	$selectedChannel->displayName = $location;
	$selectedChannel->depiction = array();
}

$event = null;
$subjects = null;

if(isset($selectedChannel) && isset($selectedChannel->services['_broadcast-meta._tcp']))
{
	$srv = $selectedChannel->services['_broadcast-meta._tcp'];
	$url = 'http://' . $srv['srv'][0]['target'] . ':' . $srv['srv'][0]['port'] . $srv['params']['path'] . '?transmissionTime=' . strftime('%Y-%m-%dT%H:%M:%SZ');
	$curl = new CurlCache($url);
	$curl->cacheTime = 60;
	$curl->headers = array('Host: ' . $selectedChannel->rdns->fqdn);
	$curl->returnTransfer = true;
	$curl->fetchHeaders = true;
	$curl->followLocation = false;
	$ret = $curl->exec();
/*	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $selectedChannel->rdns->fqdn));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);						
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	$ret = curl_exec($ch); */
	if($ret)
	{
		$headers = explode("\n", $ret);
		$location = null;
		foreach($headers as $kv)
		{
			$kv = explode(':', trim($kv), 2);
			if(count($kv) == 2)
			{
				if(!strcasecmp($kv[0], 'Location'))
				{
					$location = trim($kv[1]);
				}
			}
		}
	}
}

function fetch_po($location)
{
	$doc = RDF::documentFromURL($location);
	if(!is_object($doc))
	{
		return null;
	}
/*	ob_start();
	$doc = cache_fetch_rdf($location);
	ob_end_clean();
	$doc = RDF::documentFromXMLString($doc, $location); */
	$primary = ProgrammesOntology::instanceFromDocument($doc);
	if(!empty($_REQUEST['dump-docs']))
	{
		echo '<pre>';
		print_r($doc);
		echo '</pre>';
	}
	return $primary;
}

if(strlen($location))
{
	$event = array();
	$episode = fetch_po($location);
	$brand = $series = $page_title = null;
	if(isset($episode->series))
	{
		$series = fetch_po($episode->series);
	}
	if(isset($series->brand))
	{
		$brand = fetch_po($series->brand);
	}
	else if(isset($episode->brand))
	{
		$brand = fetch_po($episode->brand);
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
}
if(!empty($_REQUEST['dump-docs']))
{
	die();
}

$applinks = array();
require_once(dirname(__FILE__) . '/apps.php');
if(!empty($_REQUEST['dump-subjects']))
{
	echo '<pre>';
	print_r($subjects);
	die();
}
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
if(!empty($_REQUEST['dump-applinks']))
{
	echo '<pre>';
	print_r($applinks);
	die();
}

require_once(dirname(__FILE__) . '/view.phtml');
