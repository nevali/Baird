<?php

require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/services.php');

function dvbMatch($onid, $tsid = null, $sid = null, $nid = null, $list = null)
{
	global $services;
	
	if($list === null) $list = $services;
	foreach($list as $uri => $srv)
	{
		if(isset($srv['dvb']))
		{
			foreach($srv['dvb'] as $dvb)
			{
				if(strcasecmp($dvb['o'], $onid)) continue;
				if($sid !== null)
				{
					if(strcasecmp($dvb['t'], $tsid)) continue;
					if(strcasecmp($dvb['s'], $sid)) continue;
				}
				return $uri;
			}
		}
		else if(isset($srv['children']))
		{
			$uri = dvbMatch($onid, $tsid, $sid, $nid, $srv['children']);
			if(strlen($uri))
			{
				return $uri;
			}
		}
	}
	return null;
}


$endpoint = NOTUBE_QUERY_URL;
$service = null;
$start = null;
$duration = null;
$transmissionTime = null;
$query = null;
$uri = array();

/* Make debugging easier: allow service to be specified by way of
 *  a service=name[/name/name...] parameter
 */
if(isset($_GET['service']))
{
	$srv = explode('/', $_GET['service']);
	$list = $services;
	while(count($srv) && $list)
	{
		$sname = array_shift($srv);
		foreach($list as $suri => $sinfo)
		{
			if(isset($sinfo['name']) && !strcmp($sinfo['name'], $sname))
			{
				$service = $suri;
				if(isset($sinfo['children']))
				{
					$list = $sinfo['children'];
				}
				else
				{
					$list = null;
				}
				break;
			}
		}
	}
	/* Pick the first child service if it wasn't explicitly selected */
	while($list)
	{
		foreach($list as $suri => $sinfo)
		{
			$service = $suri;
			if(isset($sinfo['children']))
			{
				$list = $sinfo['children'];
			}
			else
			{
				$list = null;
			}
			break;
		}
	}
}

$host = $_SERVER['HTTP_HOST'];
if(!strlen($service))
{
	/* Match tvdns/radiodns fqdn against service list */
}

if(isset($_GET['uri']))
{
	$uris = $_GET['uri'];
	if(!is_array($uris))
	{
		if(strlen($uris))
		{
			$uris = array($uris);
		}
		else
		{
			$uris = null;
		}
	}
	if(is_array($uris))
	{
		foreach($uris as $uristr)
		{
			$match = array();
			if(preg_match('/^tag:feeds.bbc.co.uk,2008:PIPS:([a-z0-9]+)$/i', $uristr, $match) && count($match) >= 2)
			{
				$uri['pid'][] = 'pid:' . $match[1];
			}
			else if(preg_match('/^([a-z0-9-+]+):/i', $uristr, $match) && count($match) >= 2)
			{
				$uri[$match[1]][] = $uristr;
			}
		}
	}
}

if(isset($_GET['start']))
{
	if(!preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z/', $_GET['start']))
	{
		die('start value must be in %Y-%m-%dT%H:%M:%SZ format');
	}
	$start = $_GET['start'];
}

if(!strlen($service) && isset($uri['dvb']))
{
	foreach($uri['dvb'] as $u)
	{
		$dmatch = array();
		$onid = $tsid = $sid = null;
		if(preg_match('!^dvb://([a-f0-9]{1,4})(.([a-f0-9]{1,4})(.([a-f0-9]{1,4}))?)?([.$/;~].*)?$!i', $u, $dmatch))
		{
			if(isset($dmatch[1]))
			{
				$onid = $dmatch[1];
				if(isset($dmatch[3]) && isset($dmatch[5]))
				{
					$tsid = $dmatch[3];
					$sid = $dmatch[5];
				}
			}
		}
		$service = dvbMatch($onid, $tsid, $sid);
		if(strlen($service))
		{
			break;
		}
	}
}

$queryParams = array('?broadcast' => true);
$where = array();

if(strlen($service))
{
	$where[] = '?broadcast <http://purl.org/ontology/po/broadcast_on> <' . $service . '>';
}

$queryParams['?service'] = true;
$where[] = '?broadcast <http://purl.org/ontology/po/broadcast_on> ?service';

$queryParams['?version'] = true;
$where[] = '?broadcast <http://purl.org/ontology/po/broadcast_of> ?version';

$queryParams['?prog'] = true;
$where[] = 'OPTIONAL { ?prog <http://purl.org/ontology/po/version> ?version }';

$queryParams['?crid'] = true;
$where[] = 'OPTIONAL { ?crid <http://www.w3.org/2002/07/owl#sameAs> ?version . FILTER regex(str(?crid), "^crid://", "i") }';

$n = 0;
if(isset($uri['crid']))
{
	$list = array();
	foreach($uri['crid'] as $crid)
	{
		$queryParams['?uri' . $n] = true;
		$list[] = '{ ?uri' . $n . ' <http://www.w3.org/2002/07/owl#sameAs> ?version . FILTER regex(str(?uri' . $n . '), "' . $crid . '", "i") }';
	}
	$where[] = implode(' UNION ', $list);
}
if(isset($uri['pid']))
{
	$list = array();
	foreach($uri['pid'] as $tag)
	{
		$tag = substr($tag, 4);
		$queryParams['?uri' . $n] = true;
		$where[] = 'FILTER regex(str(?version), "http://www.bbc.co.uk/programmes/' . $tag . '#programme", "i")';
//		$list[] = '{ ?uri' . $n . ' <http://www.w3.org/2002/07/owl#sameAs> ?broadcast . FILTER regex(str(?uri' . $n . '), "' . $tag . '", "i") }';
	}
}

if(strlen($start))
{
	/* The time needs to be in Europe/London */
	$dt = new DateTime(substr($start, 0, 10) . ' ' . substr($start, 11, 8));
	$dt->setTimeZone(new DateTimeZone('Europe/London'));
	$start = $dt->format(DateTime::W3C);
	$where[] = '?broadcast <http://purl.org/NET/c4dm/event.owl#time> ?time';
	$where[] = '?time <http://purl.org/NET/c4dm/timeline.owl#start> "' . $start . '"^^<http://www.w3.org/2001/XMLSchema#dateTime>';
}

/* Construct a SPARQL query from our assorted parameters */
if(count($queryParams))
{
	$query = 'SELECT ' . implode(' ', array_keys($queryParams)) . ' WHERE { ' . implode(' . ', $where) . ' } LIMIT 1';
}
else
{
	die('No query parameters');
}

$result = json_decode(file_get_contents($endpoint . '?query=' . urlencode($query)), true);
$target = null;
$crid = null;
$match = null;
if($result[0] == '200' && isset($result[1]) && is_array($result[1]))
{
	foreach($result[1] as $res)
	{		
		$match = $res;
		if(isset($res['broadcast']))
		{
			$target = $res['broadcast'];
		}
		else if(isset($res['version']))
		{
			$target = $res['version'];
		}
		else if(isset($res['prog']))
		{
			$target = $res['prog'];
		}
		if(isset($res['crid']))
		{
			$crid = $res['crid'];
		}
		break;
	}
}
if(isset($_GET['kind']))
{
	switch($_GET['kind'])
	{
		case 'crid':
			header('Content-type: text/plain');
			die($crid);
		case 'results':
			header('Content-type: text/plain');
			print_r($result);
			die();
		case 'json':
			header('Content-type: application/json');		
			echo json_encode($result);
			die();
	}
}

function e()
{
	$x = func_get_args();
	echo htmlspecialchars(implode(' ', $x));
}

function _e()
{
	$x = func_get_args();
	return htmlspecialchars(implode(' ', $x));
}


?>
<!DOCTYPE html>
<html>
	<head>
		<title>What the crid…?!</title>
	</head>
	<body>
		<h1><?php if($target) { e($target); } else { e('No match'); }?></h1>
		<?php if(!empty($_GET['showQuery']))
		{
			echo '<p><code>' . _e($query) . '</code></p>';
		}
		?>
		<dl>
		<?php if(isset($match['service']))
		{
			echo '<dt>Service:</dt>';
			echo '<dd>&lt;<a href="' . _e($match['service']) . '">' . _e($match['service']) . '</a>&gt;</dd>';
		} ?>
		<?php if(isset($match['prog']))
		{
			echo '<dt>Episode:</dt>';
			echo '<dd>&lt;<a href="' . _e($match['prog']) . '">' . _e($match['prog']) . '</a>&gt;</dd>';
		} ?>
		<?php if(isset($match['version']))
		{
			echo '<dt>Version:</dt>';
			echo '<dd>&lt;<a href="' . _e($match['version']) . '">' . _e($match['version']) . '</a>&gt;</dd>';
		} ?>
		<?php if(isset($match['broadcast']))
		{
			echo '<dt>Broadcast:</dt>';
			echo '<dd>&lt;<a href="' . _e($match['broadcast']) . '">' . _e($match['broadcast']) . '</a>&gt;</dd>';
		} ?>
		<?php if(isset($match['crid']))
		{
			echo '<dt>pCRID:</dt>';
			echo '<dd>&lt;<a href="' . _e($match['crid']) . '">' . _e($match['crid']) . '</a>&gt;</dd>';
		} ?>
		</dl>
		<ul>
		<?php if(isset($match['prog']))
		{
			echo '<li><a href="http://atlasapi.org/2.0/items.html?uri=' . _e($match['prog']) . '">Find media using Atlas</a></li>';
			$matches = array();
			if(preg_match('!^http://www.bbc.co.uk/programmes/([a-z0-9]+)#!i', $match['prog'], $matches) && count($matches) >= 2)
			{
				echo '<li><a href="http://www.bbc.co.uk/iplayer/episode/' . $matches[1] . '">Watch on iPlayer</a> (if available)</li>';
			}
		} ?>
		<?php if(isset($match['crid']))
		{
			echo '<li><a href="http://g.bbcredux.com/search?repeats=on&pcrid=' . _e($match['crid']) . '">Find media using Redux</a> (BBC staff only)</li>';
		} ?>
		</ul>
	</body>
</html>
