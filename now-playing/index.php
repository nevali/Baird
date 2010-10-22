<?php

require_once(dirname(__FILE__) . '/config.php');
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
			$channel['fqdn'] = $channel['nid'] . '.' . $channel['sid'] . '.' . $channel['tsid'] . '.' . $channel['onid'] . '.dvb.tvdns.net';
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
	$db = DBCore::connect(CRIDS_IRI);
	$row = $db->row('SELECT * FROM {crids} WHERE "active" = 1 AND "service" = ? AND "start" <= NOW() ORDER BY "start" DESC', $channel['name']);
	
	if($row)
	{
		$nowp['crid'] = $row['crid'];
		$nowp['dvb'] = $row['dvb'];
		$nowp['start'] = strftime('%Y-%m-%dT%H:%M:%SZ', strtotime($row['start']));
		$nowp['service'] = $channel['service'];
		$nowp['fqdn'] = $channel['fqdn'];
	}
}

$links = array();
if(isset($nowp))
{
	if(strlen($nowp['crid']))
	{
		$links[] = '<' . $nowp['crid'] . '>; rel="http://purl.org/ontology/po/Version"';
	}
	$links[] = '<' . $nowp['dvb'] . '>; rel="http://purl.org/ontology/po/Broadcast"; scheduledStart="' . $nowp['start'] . '"';
	$links[] = '<' . $nowp['service'] . '>; rel="http://purl.org/ontology/po/Channel"';
	$links[] = '<dns:' . $nowp['fqdn'] . '>; rel="http://purl.org/ontology/po/Channel"';
}

header('X-Device-DisplayName: Televisor');
header('Link: ' . implode(',', $links));

require_once(dirname(__FILE__) . '/view.phtml');
