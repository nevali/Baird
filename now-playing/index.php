<?php

define('MODULES_ROOT', dirname(__FILE__) . '/../app/');
define('CACHE_DIR', dirname(__FILE__) . '/../cache/');
define('CACHE_TIME', 3600);

require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(dirname(__FILE__) . '/../boxify/channel.php');
require_once(dirname(__FILE__) . '/../boxify/dvb.php');
require_once(dirname(__FILE__) . '/../boxify/ip.php');

uses('curl');

$onid = '233a';
$nid = '3098';
$chan = 1;
$channel = null;

if(isset($_REQUEST['chan']))
{
	$chan = intval($_REQUEST['chan']);
}

foreach($platform['dvb']['onid'][$onid]['nid'][$nid]['tsid'] as $tsid => $ts)
{
	foreach($ts['sid'] as $sid => $service)
	{
		if(empty($chan) || $service['lcn'] == $chan)
		{
			$channel = $service;
			$channel['onid'] = $onid;
			$channel['nid'] = $nid;
			$channel['tsid'] = $tsid;
			$channel['sid'] = $sid;
			$channel['service'] = 'dvb://' . $channel['onid'] . '.' . $channel['tsid'] . '.' . $channel['sid'];
			break;
		}
	}
	if($channel)
	{
		break;
	}
}

$nowp = null;

if($channel)
{
	$fqdn = $channel['nid'] . '.' . $channel['sid'] . '.' . $channel['tsid'] . '.' . $channel['onid'] . '.dvb.tvdns.net';
	$url = 'http://services.notu.be/resolve?transmissionTime=&noredirect=1';
	$curl = new CurlCache($url);
	$curl->cacheTime = 60;
	$curl->headers = array('Host: ' . $fqdn);
	$curl->returnTransfer = true;
	$curl->fetchHeaders = false;
	$curl->followLocation = false;
	$ret = json_decode($curl->exec(), true);
	if(!empty($_REQUEST['dump-raw']))
	{
		echo '<pre>';
		print_r($ret);
		die();
	}
	if(is_array($ret) && $ret[0] == 200)
	{
		foreach($ret[1] as $entry)
		{
			if(isset($entry['dvb']) && strncmp($entry['dvb'], 'dvb:', 4))
			{
				continue;
			}
			$start = explode('^', $entry['start']);
			if(strncmp($start[0], strftime('%Y-%m-%d'), 10))
			{
				continue;
			}
			$nowp['crid'] = $entry['crid'];
			$nowp['dvb'] = $entry['dvb'];
			$nowp['start'] = $start[0];
			$nowp['service'] = $channel['service'];
			$nowp['fqdn'] = $fqdn;
			break;
		}
	}
}

$links = array();
if(isset($nowp))
{
	$links[] = '<' . $nowp['crid'] . '>; rel="http://purl.org/ontology/po/Version"';
	$links[] = '<' . $nowp['dvb'] . '>; rel="http://purl.org/ontology/po/Broadcast"; scheduledStart="' . $nowp['start'] . '"';
	$links[] = '<' . $nowp['service'] . '>; rel="http://purl.org/ontology/po/Channel"';
	$links[] = '<dns:' . $nowp['fqdn'] . '>; rel="http://purl.org/ontology/po/Channel"';
}

header('Link: ' . implode(',', $links));

require_once(dirname(__FILE__) . '/view.phtml');
