<?php

define('MODULES_ROOT', dirname(__FILE__) . '/../app/');
require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(MODULES_ROOT . 'radiodns/radiodns.php');
require_once(MODULES_ROOT . 'xrd/xrd.php');
require_once(dirname(__FILE__) . '/../boxify/channel.php');
require_once(dirname(__FILE__) . '/../boxify/dvb.php');
require_once(dirname(__FILE__) . '/../boxify/ip.php');

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

while(count($sources))
{
	$source = array_shift($sources);
	switch($source['kind'])
	{
		case 'dvb':
			DVB::addChannelsFromSource($listing, $source['onid'], $source['nid'], $source['tsid'], $source['sid']);
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
if(!$selectedChannel)
{
	trigger_error('No channel selected', E_USER_ERROR);
	die(1);
}

/* Fetch the XRD, if available */
if(empty($selectedChannel->xrdFetched) && isset($selectedChannel->services['_xrd._tcp']))
{
	$selectedChannel->xrdFetched = true;
	$xrds->mergeFrom(XRDS::xrdsFromURI($chan->services['_xrd._tcp']));
}

$xrds->locateParents();
$xrds->matchPlatformsAgainstListing($listing);
$listing->matchXRDS($xrds);

$event = null;

if(isset($selectedChannel->services['_broadcast-meta._tcp']))
{
	$srv = $selectedChannel->services['_broadcast-meta._tcp'];
	$url = 'http://' . $srv['srv'][0]['target'] . ':' . $srv['srv'][0]['port'] . $srv['params']['path'] . '?transmissionTime=' . strftime('%Y-%m-%dT%H:%M:%SZ');
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $selectedChannel->rdns->fqdn));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);						
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	$ret = curl_exec($ch);
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
		if(strlen($location))
		{
			if(($x = strrpos($location, '#')) !== false)
			{
				$location = substr($location, 0, $x);
			}
			/* Sod it */
			$location .= '.json';
			$ch = curl_init($location);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_NOBODY, false);
			$ret = curl_exec($ch);
			$event = json_decode($ret, true);
		}
	}
}

$d = new XRDLink('bbcone.png', 'image/png');
$d->height = 18;
$d->preferredBackground = '#ae1005';

$selectedChannel->depiction[] = $d;

require_once(dirname(__FILE__) . '/view.phtml');
