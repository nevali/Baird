<?php

$services = array(
	'_radioepg._tcp' => 'RadioEPG', 
	'_radiovis._tcp' => 'RadioVIS',
	'_radiotag._tcp' => 'RadioTAG',
	'_broadcast-meta._tcp' => 'URI Resolver',
	'_xrd._tcp' => 'Service manifest',
	'_http._tcp' => 'Web page',
);

$platform = array();
$channels = array();
$targets = array();
$xrd = array();
$resolver = array();
$kind = $onid = $nid = null;
$suffix = 'tvdns.net';
require_once(dirname(__FILE__) . '/common.php');
require_once(dirname(__FILE__) . '/dvb/data.php');

$types = array();
$onids = array();
$nids = array();
foreach($platform as $k => $v)
{
	$types[$k] = $v['name'];
	foreach($v['onid'] as $onid => $info)
	{
		$onids[] = array('p' => $k, 'o' => $onid, 'name' => $info['name']);
		foreach($info['nid'] as $nid => $ninfo)
		{
			$nids[] = array('p' => $k, 'o' => $onid, 'n' => $nid, 'name' => $ninfo['name']);
		}
	}
}


if(!empty($_REQUEST['kind']) && !empty($_REQUEST['onid']) && !empty($_REQUEST['nid']))
{
	$kind = trim($_REQUEST['kind']);
	$onid = trim($_REQUEST['onid']);
	$nid = trim($_REQUEST['nid']);
	if(!isset($platform[$kind]['onid'][$onid]['nid'][$nid]))
	{
		die('The specified network does not exist');
		$kind = $onid = $nid = null;
	}
}

if($kind)
{
	foreach($platform[$kind]['onid'][$onid]['nid'][$nid]['tsid'] as $tsid => $ts)
	{
		foreach($ts['sid'] as $sid => $channel)
		{
			$channel['kind'] = $kind;
			if($channel['data'])
			{
				$channel['serviceClass'] = 'interactive';
			}
			else
			{
				$channel['serviceClass'] = 'linear';
			}
			$channel['available'] = true;
			$channel['tsname'] = $ts['name'];
			$channel['fqdn'] = $nid . '.' . $sid . '.' . $tsid . '.' . $onid . '.' . $kind . '.' . $suffix;
			$channel['uri'] = 'dvb://' . $onid . '. ' . $tsid . '.' . $sid;
			$channel['lookup'] = '/lookup/?kind=dvb&original_network_id=' . $onid . '&network_id=' . $nid . '&transport_stream_id=' . $tsid . '&service_id=' . $sid;
			$channel['ota'] = true;
			$channel['target'] = null;
			$domain = $channel['fqdn'];
			do
			{
//				echo "<p>Looking for records for " . $domain  . "</p>";
				if(($records = dns_get_record($domain)))
				{
/*					echo '<pre>';
					echo $domain . "\n";
					print_r($records);
					echo '</pre>'; */
					foreach($records as $rec)
					{
						if(isset($rec['type']) && $rec['type'] == 'CNAME')
						{
							$channel['target'] = $rec['target'];
//							echo '<p>Found CNAME: ' . $rec['target'] . '</p>';
							/* Don't break - we want the last record */
						}
					}
				}
				if(strlen($channel['target']) && strcmp($domain, $channel['target']))
				{
					$domain = $channel['target'];
//					echo '<p>Will loop for ' . $domain . '</p>';
					continue;
				}
				break;
			}
			while(true);
//			echo '<p>Target is ' . $channel['target'] . '</p>';
			if(isset($channel['target']))
			{
				$targ = $channel['target'];
				if(isset($targets[$targ]))
				{
					$channel['targetIndex'] = $targets[$targ]['index'];
				}
				else
				{
					$channel['targetIndex'] = count($targets);
					$targets[$channel['target']] = array('index' => $channel['targetIndex']);
				}
			}
			$channels[sprintf('%04d', $channel['lcn'])] = $channel;
		}
	}
}

ksort($channels);

foreach($targets as $fqdn => $info)
{
	foreach($services as $srv => $name)
	{
		$recs = dns_get_record($srv . '.' . $fqdn);
		$service = array('name' => $name, 'records' => array(), 'params' => array(), 'targets' => array());
		foreach($recs as $r)
		{
			if(isset($r['type']) && $r['type'] != 'CNAME' && $r['type'] != 'SOA')
			{
				$service['records'][] = $r;
				if($r['type'] == 'TXT')
				{
					$plist = explode(' ', $r['txt']);
					foreach($plist as $p)
					{
						$p = trim($p);
						if(!strlen($p)) continue;
						$kv = explode('=', $p, 2);
						if(count($kv) != 2) continue;
						$service['params'][$kv[0]] = $kv[1];
					}
				}
				else if($r['type'] == 'SRV')
				{
					$k = sprintf('%04d-%04d', $r['pri'], $r['weight']);
					$service['targets'][$k] = array('host' => $r['target'], 'port' => $r['port']);
				}
			}
		}
		if(count($service['targets']))
		{
			$info['services'][$srv] = $service;
		}
	}
	if(isset($info['services']['_broadcast-meta._tcp']))
	{
		foreach($channels as $k => $chan)
		{
			if($chan['targetIndex'] == $info['index'])
			{
				$channels[$k]['resolver'] = true;
			}
		}
	}
	if(isset($info['services']['_xrd._tcp']))
	{
		$uri = null;
		if(isset($info['services']['_xrd._tcp']['params']['path']))
		{
			$p = $info['services']['_xrd._tcp']['params']['path'];
			if(substr($p, 0, 1) != '/') $p = '/' . $p;
			foreach($info['services']['_xrd._tcp']['targets'] as $targ)
			{
				$uri = 'http://' . $targ['host'] . ':' . $targ['port'] . $p;
				break;
			}
		}
		if(strlen($uri))
		{
			$xrd[$uri] = array();
		}
	}
	if(isset($info['services']['_http._tcp']))
	{
		$uri = null;
		$host = null;
		if(isset($info['services']['_http._tcp']['params']['path']))
		{
			$p = $info['services']['_http._tcp']['params']['path'];
			if(substr($p, 0, 1) != '/') $p = '/' . $p;
			foreach($info['services']['_http._tcp']['targets'] as $targ)
			{
				$uri = 'http://' . $targ['host'] . ':' . $targ['port'] . $p;
				$host = $targ['host'];
				break;
			}
		}
		if(strlen($uri))
		{
			foreach($channels as $k => $chan)
			{
				if($chan['targetIndex'] == $info['index'])
				{
					$channels[$k]['website'] = $uri;
					$channels[$k]['websiteHost'] = $host;
				}
			}
		}
	}
	$targets[$fqdn] = $info;
}

require_once(dirname(__FILE__) . '/view.phtml');