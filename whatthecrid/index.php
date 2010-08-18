<?php

require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(dirname(__FILE__) . '/config.php');

$endpoint = NOTUBE_QUERY_URL;

// The list of service identifiers that we have responsibility for resolving for

$services = array(
	'http://www.bbc.co.uk/services/bbcone#service' => array(
		'name' => 'bbcone',
		'dvb' => array(),
		'children' => array(
			'http://www.bbc.co.uk/services/bbcone/west#service' => array(
				'name' => 'west',
				'dvb' => array(
					array('n' => '3098', 's' => '1041', 't' => '1041', 'o' => '233a'),
				),
			),
			'http://www.bbc.co.uk/services/bbcone/south_west#service' => array(
				'name' => 'south_west',
			),
			'http://www.bbc.co.uk/services/bbcone/london#service' => array(
				'name' => 'london',
			),
			'http://www.bbc.co.uk/services/bbcone/west_midlands#service' => array(
				'name' => 'west_midlands',
			),
			'http://www.bbc.co.uk/services/bbcone/east_midlands#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/east#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/north_west#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/north_east#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/yorkshire#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/oxford#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/south_east#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/channel_islands#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/east_yorkshire#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/scotland#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/wales#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/ni#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/south#service' => array(),
			'http://www.bbc.co.uk/services/bbcone/cambridge#service' => array(),
		),
	),
);

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
			if(preg_match('/^([a-z0-9-+]+):/i', $uristr, $match) && count($match) >= 2)
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

if(!$service)
{
	die('no service');
}

$queryParams = array('?version' => true, '?broadcast' => true);
$where = array();

$where[] = '?broadcast <http://purl.org/ontology/po/broadcast_on> <' . $service . '>';
$where[] = '?broadcast <http://purl.org/ontology/po/broadcast_of> ?version';

$queryParams['?crid'] = true;
$where[] = 'OPTIONAL { ?crid <http://www.w3.org/2002/07/owl#sameAs> ?version . FILTER regex(str(?crid), "^crid://", "i") }';

if(isset($uri['crid']))
{
	$list = array();
	$n = 0;
	foreach($uri['crid'] as $crid)
	{
		$queryParams['?uri' . $n] = true;
		$list[] = '{ ?uri' . $n . ' <http://www.w3.org/2002/07/owl#sameAs> ?version . FILTER regex(str(?uri' . $n . '), "' . $crid . '", "i") }';
	}
	$where[] = implode(' UNION ', $list);
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
	$query = 'SELECT ' . implode(' ', array_keys($queryParams)) . ' WHERE { ' . implode(' . ', $where) . ' }';
}
else
{
	die('No query parameters');
}

header('Content-type: text/plain');

// echo $query . "\n";

$result = json_decode(file_get_contents($endpoint . '?query=' . urlencode($query)), true);
$target = null;
$crid = null;
if($result[0] == '200' && isset($result[1]) && is_array($result[1]))
{
	foreach($result[1] as $res)
	{
		if(isset($res['broadcast']))
		{
			$target = $res['broadcast'];
		}
		else if(isset($res['version']))
		{
			$target = $res['version'];
		}
		else if(isset($res['p']))
		{
			$target = $res['p'];
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
			die($crid);
	}
}
print_r($result);
die($target);

