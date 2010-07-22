<?php

$xrdUrls = array(
	'freeview-sample' => 'http://projectbaird.com/applications/manifests/sample-freeview.xml',
	'freeview-sample-dev' => 'http://baird.nx/applications/manifests/sample-freeview.xml',
	'bbc-sample' => 'http://projectbaird.com/applications/manifests/sample-bbc.xml',
	'bbc-sample-dev' => 'http://baird.nx/applications/manifests/sample-bbc.xml',
	'seesaw-sample' => 'http://projectbaird.com/applications/manifests/sample-seesaw.xml',
	'seesaw-sample-dev' => 'http://baird.nx/applications/manifests/sample-seesaw.xml',
	'itv-sample' => 'http://projectbaird.com/applications/manifests/sample-itv.xml',
	'itv-sample-dev' => 'http://baird.nx/applications/manifests/sample-itv.xml',
);

$sources = array();
$platform = array();
$xrd = array();
$xrdFetched = array();
$alerts = array();
$haveXRD = false;
$processXRD = false;

$kind = $onid = $nid = $ecc = $region = null;

define('MODULES_ROOT', dirname(__FILE__) . '/../app/');
require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(MODULES_ROOT . 'radiodns/radiodns.php');
require_once(MODULES_ROOT . 'xrd/xrd.php');
require_once(dirname(__FILE__) . '/channel.php');
require_once(dirname(__FILE__) . '/dvb.php');
require_once(dirname(__FILE__) . '/fm.php');
require_once(dirname(__FILE__) . '/ip.php');

$listing = new ChannelListing();
$xrds = new ChannelXRDS();

if(!empty($_REQUEST['kind']) && $_REQUEST['kind'] == 'dvb' && !empty($_REQUEST['onid']) && !empty($_REQUEST['nid']))
{
	$kind = 'dvb';
	$onid = trim($_REQUEST['onid']);
	$nid = trim($_REQUEST['nid']);
	if(isset($platform[$kind]['onid'][$onid]['nid'][$nid]))
	{
		$sources[] = array('kind' => 'dvb', 'onid' => $onid, 'nid' => $nid);
		$platform['dvb://'. $onid] = array('kind' => 'dvb', 'onid' => $onid);
	}
	else
	{
		$alerts[] = 'The specified DVB network does not exist';
		$kind = $onid = $nid = null;
	}
	
}
if(!empty($_REQUEST['kind']) && $_REQUEST['kind'] == 'fm' && !empty($_REQUEST['ecc']) && !empty($_REQUEST['region']))
{
	$kind = 'fm';
	$ecc = trim($_REQUEST['ecc']);
	$region = strtolower(trim($_REQUEST['region']));
	if(isset($platform[$kind]['ecc'][$ecc]['region'][$region]))
	{
		$platform['fm://' . $ecc] = array('kind' => 'fm', 'ecc' => $ecc);
		$sources[] = array('kind' => 'fm', 'ecc' => $ecc, 'region' => $region);
	}
	else
	{
		$alerts[] = 'The specified FM network does not exist';
		$kind = $ecc = $region = null;
	}
}

if(isset($_REQUEST['xrdurl']))
{
	$urls = $_REQUEST['xrdurl'];
	if(!is_array($urls))
	{
		$urls = array($urls);
	}
	foreach($urls as $url)
	{
		if(isset($xrdUrls[$url]))
		{
			$sources[] = array('kind' => 'xrd', 'url' => $xrdUrls[$url]);
		}
	}
}

if(!empty($_REQUEST['xrd']))
{
	$processXRD = true;
}

/* Build the initial channel line-up from the list of sources */
while(count($sources))
{
	$source = array_shift($sources);
	switch($source['kind'])
	{
		case 'xrd':
			$uri = $source['url'];
			if(isset($xrdFetched[$uri])) continue;
			$xrdFetched[$uri] = true;
			$xrds->mergeFrom(XRDS::xrdsFromURI($uri));
			break;
		case 'fm':
			FM::addChannelsFromSource($listing, $source['ecc'], $source['region']);
			break;
		case 'dvb':
			DVB::addChannelsFromSource($listing, $source['onid'], $source['nid']);
			break;
		default:
			trigger_error('Unsupported source kind ' . $source['kind'], E_USER_WARNING);
	}
	$channels = $listing->channels();
	foreach($channels as $chan)
	{
		if(empty($chan->xrdFetched) && isset($chan->services['_xrd._tcp']))
		{
			$chan->xrdFetched = true;
			$haveXRD = true;
			if($processXRD)
			{
				if(isset($xrdFetched[$chan->services['_xrd._tcp']])) continue;
				$sources[] = array('kind' => 'xrd', 'url' => $chan->services['_xrd._tcp']);
			}
		}
	}
}

/* $xrds->recurse(); */
$xrds->locateParents();
$xrds->matchPlatformsAgainstListing($listing);
$listing->matchXRDS($xrds);
$xrds->addIPServicesToListing($listing);

/* 		if(isset($entry['links']['self']))
		{
			foreach($entry['links']['self'] as $link)
			{
				if($link['type'] == 'application/xrd+xml')
				{
					$xrd[$link['href']] = $link['href'];
				}
			}
		}
*/


/*
foreach($xrdServices as $service)
{
	// Skip entries which already exist in the line-up
	if(count($service['channels'])) continue;
	// Skip entries which don't have a label
	if(!isset($service['label'])) continue;
	// Skip entries which don't have a service class
	if(!isset($service['serviceClass'])) continue;
	
	$channel = null;
	$parent = null;
	if(isset($service['parent']))
	{
		$parent = $xrdServices[$service['parent']];
		if(!isset($service['links']['urn:tva:metadata:2005:ServiceGenre']) && isset($parent['links']['urn:tva:metadata:2005:ServiceGenre']))
		{
			$service['links']['urn:tva:metadata:2005:ServiceGenre'] = $parent['links']['urn:tva:metadata:2005:ServiceGenre'];
		}
		
	}
	// On-demand and interactive services use a link relation of 'self'
	if(($service['serviceClass'] == 'demand' || $service['serviceClass'] == 'interactive') && isset($service['links']['self'][0]))
	{
		$channel = array(
			'kind' => 'ip',
			'serviceClass' => $service['serviceClass'],
			'name' => $service['label'],
			'subject' => $service['subject'],
			'parent' => $service['parent'],
			'uri' => $service['links']['self'][0]['href'],
			'fqdn' => null,
			'target' => null,
			'lookup' => '/lookup/?kind=ip&url=' . urlencode($service['links']['self'][0]['href']),
			'streams' => $service['links']['self'],
		);
	}
	// Linear services use a link relation of http://purl.org/ontology/po/IPStream
	if($service['serviceClass'] == 'linear' && isset($service['links']['http://purl.org/ontology/po/IPStream'][0]))
	{
		$channel = array(
			'kind' => 'ip',
			'serviceClass' => $service['serviceClass'],
			'name' => $service['label'],
			'subject' => $service['subject'],
			'parent' => $service['parent'],
			'uri' => $service['links']['http://purl.org/ontology/po/IPStream'][0]['href'],
			'fqdn' => null,
			'target' => null,
			'lookup' => '/lookup/?kind=ip&url=' . urlencode($service['links']['http://purl.org/ontology/po/IPStream'][0]['href']),
			'streams' => $service['links']['http://purl.org/ontology/po/IPStream'],
		);
	}	
	if($channel)
	{
		if(isset($service['links']['urn:tva:metadata:2005:ServiceGenre']))
		{
			foreach($service['links']['urn:tva:metadata:2005:ServiceGenre'] as $link)
			{
				if(!strcmp($link['href'], 'urn:tva:metadata:cs:MediaTypeCS:2005:7.1.1'))
				{
					$channel['audio'] = true;
				}
			}
		}
	}
	if($channel)
	{
		$chanNumber = $nextDynamicChannel;
		if(isset($service['parent']))
		{
			foreach($channels as $chan)
			{
				if($chan['parent'] == $service['parent'])
				{
					$extraChannels[] = $channel;
					$channel = null;
					break;
				}
			}
		}
	}
	if($channel)
	{
		if(isset($service['props']['http://projectbaird.com/ns/serviceNumberPreference']))
		{
			foreach($service['props']['http://projectbaird.com/ns/serviceNumberPreference'] as $int)
			{
				$int = intval($int);
				if(!$int) continue;
				if($int >= $firstDynamicChannel) continue;
				$k = sprintf('%04d', $int);
				if(isset($channels[$k])) continue;
				$chanNumber = $int;
				break;
			}
		}			
		$channel['lcn'] = $chanNumber;
		$channels[sprintf('%04d', $chanNumber)] = $channel;
		if($chanNumber == $nextDynamicChannel)
		{
			$nextDynamicChannel++;
		}
	}
}
*/

/* Merge XRD data */
/* foreach($channels as $k => $chan)
{
	if(isset($chan['subject']))
	{
		$service = $xrdServices[$chan['subject']];
		$parent = null;
		if(isset($service['parent']))
		{
			$parent = $xrdServices[$service['parent']];
		}
		if(isset($parent['label']) && isset($service['label']))
		{
			$channels[$k]['name'] = $parent['label'] . ' (' . $service['label'] . ')';
		}
		else if(isset($parent['label']))
		{
			$channels[$k]['name'] = $parent['label'];
		}
		else if(isset($service['label']))
		{
			$channels[$k]['name'] = $service['label'];
		}
		if(isset($service['links']['http://xmlns.com/foaf/0.1/depiction'][0]))
		{
			$channels[$k]['depiction'] = $service['links']['http://xmlns.com/foaf/0.1/depiction'][0]['href'];
		}
		else if(isset($parent['links']['http://xmlns.com/foaf/0.1/depiction'][0]))
		{
			$channels[$k]['depiction'] = $parent['links']['http://xmlns.com/foaf/0.1/depiction'][0]['href'];
		}
		if(isset($service['links']['http://purl.org/ontology/po/IPStream']))
		{
			$channels[$k]['streams'] = $service['links']['http://purl.org/ontology/po/IPStream'];
		}
		if(strlen($service['website']))
		{
			$channels[$k]['website'] = $service['website'];
			$channels[$k]['websiteHost'] = $service['websiteHost'];
		}		
	}
}
*/

require_once(dirname(__FILE__) . '/view.phtml');
