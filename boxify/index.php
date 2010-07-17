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
$xrdServices = array();
$resolver = array();
$kind = $onid = $nid = null;
$suffix = 'tvdns.net';
$nextDynamicChannel = 901;

require_once(dirname(__FILE__) . '/common.php');
require_once(dirname(__FILE__) . '/dvb/data.php');
require_once(dirname(__FILE__) . '/ip/data.php');

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
			$channel['uri'] = 'dvb://' . $onid . '.' . $tsid . '.' . $sid;
			$channel['lookup'] = '/lookup/?kind=dvb&original_network_id=' . $onid . '&network_id=' . $nid . '&transport_stream_id=' . $tsid . '&service_id=' . $sid;
			$channel['ota'] = true;
			$channel['target'] = null;
			$channel['subject'] = null;
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
			$xrd[$uri] = $uri;
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

if(!empty($_REQUEST['xrd']))
{
	foreach($xrd as $uri)
	{
		$xml =  simplexml_load_file($uri);
		if(is_object($xml))
		{
			if($xml->getName() == 'XRD')
			{
				$entries = array(xrd_parse($xml));
			}
			else
			{
				$entries = array();
				foreach($xml->XRD as $x)
				{
					$entries[] = xrd_parse($x);
				}
			}
			foreach($entries as $entry)
			{
				if(strlen($entry['subject']))
				{
					$xrdServices[$entry['subject']] = $entry;
				}
			}
//			print_r($xrdServices);
//			die();
		}
	}
}

foreach($xrdServices as $subject => $service)
{
	$svcClass = null;
	$service['channels'] = array();
	if(isset($service['links']['http://purl.org/ontology/po/DVB']))
	{
		foreach($service['links']['http://purl.org/ontology/po/DVB'] as $dvb)
		{
			foreach($channels as $k => $chan)
			{
				if(!strcmp($chan['uri'], $dvb['href']))
				{
					$service['channels'][$k] = $k;
					$channels[$k]['subject'] = $subject;
					$svcClass = $chan['serviceClass'];
				}
			}
		}
	}
	if(isset($service['links']['http://purl.org/ontology/po/parent_service'][0]))
	{
		$parentUri = $service['links']['http://purl.org/ontology/po/parent_service'][0]['href'];
		if(isset($xrdServices[$parentUri]))
		{
			$service['parent'] = $parentUri;
		}
	}
	if(isset($service['props']['http://www.w3.org/2000/01/rdf-schema#label'][0]))
	{
		$service['label'] = $service['props']['http://www.w3.org/2000/01/rdf-schema#label'][0];
	}
	if(isset($service['props']['http://projectbaird.com/ns/serviceClass'][0]))
	{
		$service['serviceClass'] = $service['props']['http://projectbaird.com/ns/serviceClass'][0];
	}
	if(!isset($service['serviceClass']))
	{
		$service['serviceClass'] = $svcClass;
	}
	$xrdServices[$subject] = $service;
}

foreach($xrdServices as $subject => $service)
{
	if(!isset($service['serviceClass']))
	{
		if(isset($service['parent']) && isset($xrdServices[$service['parent']]['serviceClass']))
		{
			$xrdServices[$subject]['serviceClass'] = $xrdServices[$service['parent']]['serviceClass'];
		}
	}
}

foreach($xrdServices as $service)
{
	/* Skip entries which already exist in the line-up */
	if(count($service['channels'])) continue;
	/* Skip entries which don't have a label */
	if(!isset($service['label'])) continue;
	/* Skip entries which don't have a service class */
	if(!isset($service['serviceClass'])) continue;
	
	$channel = null;
	/* On-demand services use a link relation of 'self' */
	
	if($service['serviceClass'] == 'demand' && isset($service['links']['self'][0]))
	{
		$channel = array(
			'kind' => 'ip',
			'serviceClass' => $service['serviceClass'],
			'name' => $service['label'],
			'subject' => $service['subject'],
			'uri' => $service['links']['self'][0]['href'],
			'fqdn' => null,
			'target' => null,
			'lookup' => '/lookup/?kind=ip&url=' . urlencode($service['links']['self'][0]['href']),
			'streams' => $service['links']['self'],
		);
	}
	if($channel)
	{
		$channel['lcn'] = $nextDynamicChannel;
		$channels[sprintf('%04d', $nextDynamicChannel)] = $channel;
		$nextDynamicChannel++;
	}
}


/* Merge XRD data */
foreach($channels as $k => $chan)
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
	}
}

function xrd_parse($node)
{
	$entry = array(
		'subject' => null,
		'props' => array(),
		'links' => array(),
	);
	$entry['subject'] = trim($node->Subject);
	foreach($node->Link as $l)
	{
		$a = $l->attributes();
		$k = trim($a->rel);
		if(!strlen($k)) continue;
		$entry['links'][$k][] = array('href' => trim($a->href), 'type' => trim($a->type));
	}
	foreach($node->Property as $prop)
	{
		$a = $prop->attributes();
		$k = trim($a->type);
		if(!strlen($k)) continue;
		$entry['props'][$k][] = trim($prop);
	}
	return $entry;
}

ksort($channels);


require_once(dirname(__FILE__) . '/view.phtml');
