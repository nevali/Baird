<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('UTC');
ini_set('default_charset', 'UTF-8');

$query_string = null;
if(isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']))
{
	$query_string = '?' . $_SERVER['QUERY_STRING'];
}

$services = array(
	'_radioepg._tcp' => 'RadioEPG', 
	'_radiovis._tcp' => 'RadioVIS',
	'_radiotag._tcp' => 'RadioTAG',
	'_broadcast-meta._tcp' => 'URI Resolver',
	'_xrd._tcp' => 'Service manifest',
	'_http._tcp' => 'Web page',
);

$alerts = array();
$invalid = array();
$fields = array(
	'kind' => null,
	'sid' => null,
	'suffix' => null,
	/* DVB SI */
	'original_network_id' => null,
	'transport_stream_id' => null,
	'service_id' => null,
	'network_id' => null,
	'enc' => null,
	/* VHF/FM */
	'freq' => null,
	'pi' => null,
	'country' => null,
	/* DAB */
	'ecc' => null,
	'eid' => null,
	'sid' => null,
	'scids' => null,
	'appty-uatype' => null,
	'pa' => null,
	/* DRM */
	'drm-sid' => null,
	/* AMSS */
	'amss-sid' => null,
	/* HD Radio */
	'tx' => null,
	'cc' => null,
	/* IP-delivered stream */
	'url' => null,
	/* Service-specific parameters */
	'bm:uris' => null,
	'bm:start' => null,
	'bm:duration' => null,
	'bm:time' => null,	
);

$fqdn = array();
$svc = null;
$records = null;
$target = null;
$discovered = array();

/* Ingest query parameters */
foreach($fields as $k => $v)
{
	if(isset($_REQUEST[$k]) && !is_array($_REQUEST[$k]))
	{
		$v = trim($_REQUEST[$k]);
		if(get_magic_quotes_gpc())
		{
			$v = stripslashes($v);
		}
		$fields[$k] = $v;
	}
}

switch($fields['kind'])
{
	case 'dvb':
		if(!strlen($fields['enc'])) $fields['enc'] = 'hex';
		if(!strlen($fields['suffix']))
		{
			$fields['suffix'] = 'tvdns.net';
		}
		$f = array('network_id', 'service_id', 'transport_stream_id', 'original_network_id');
		foreach($f as $k)
		{
			if(!strlen($fields[$k]))
			{
				$alerts[] = $k . ' is a required field';
				continue;
			}
			if(!ctype_xdigit($fields[$k]))
			{
				$alerts[] = $k . ' must be a 16-bit ' . ($fields['enc'] == 'dec' ? 'decimal' : 'hexadecimal') . ' unsigned integer';
				continue;
			}
			if($fields['enc'] == 'dec')
			{
				if(!ctype_digit($fields[$k]))
				{
					$alerts[] = $k . ' must be a 16-bit decimal unsigned integer';
					continue;
				}
				$v = intval($fields[$k]);
			}
			else
			{
				$v = intval($fields[$k], 16);
			}
			if($v < 0 || $v > 65535)
			{
				$alerts[] = $f . ' must be a 16-bit ' . ($fields['enc'] == 'dec' ? 'decimal' : 'hexadecimal') . ' unsigned integer';
				continue;			
			}
			$fqdn[] = sprintf('%04x', $v);
		}
		if(count($alerts))
		{
			$fqdn = array();
			break;
		}
		$svc = 'dvb://' . $fqdn[3] . '.' . $fqdn[2] . '.' . $fqdn[1];
		$fqdn[] = 'dvb';
		$fqdn[] = $fields['suffix'];
		break;
	case 'fm':
		if(!strlen($fields['suffix']))
		{
			$fields['suffix'] = 'radiodns.org';
		}
		$f = array('freq', 'pi', 'country');
		foreach($f as $k)
		{
			if(!strlen($fields[$k]))
			{
				$alerts[] = $k . ' is a required field';
				continue;
			}
			$v = $fields[$k];
			if($k == 'freq')
			{
				if(!ctype_digit($v))
				{
					$alerts[] = 'The frequency must be specified as a whole number, in units of 10kHz';
					continue;
				}
				$v = sprintf('%05d', $fields[$k]);
			}
			else if($k == 'pi')
			{
				if(!ctype_xdigit($v) || strlen($v) != 4)
				{
					$alerts[] = 'The programme information code must be a 4-digit hexadecimal value';
					continue;
				}
			}
			$fqdn[] = $v;
		}
		if(count($alerts))
		{
			$fqdn = array();
			break;
		}
		$fqdn[] = 'fm';
		$fqdn[] = $fields['suffix'];
		break;
}

if(is_array($fqdn))
{
	if(count($fqdn))
	{
		$fqdn = implode('.', $fqdn);
	}
	else
	{
		$fqdn = null;
	}
}

if(strlen($fqdn))
{
	$target = $fqdn;
	$records = dns_get_record($fqdn);
	foreach($records as $rec)
	{
		if(isset($rec['type']) && $rec['type'] == 'CNAME')
		{
			$target = $rec['target'];
			/* Don't break - we want the last record */
		}
	}
	foreach($services as $srv => $info)
	{
		if(!is_array($info))
		{
			$info = array('name' => $info);
		}
		$info['records'] = array();
		$info['fqdn'] = $fqdn;
		$info['target'] = $target;
		$recs = dns_get_record($srv . '.' . $target);
		foreach($recs as $r)
		{
			if(isset($r['type']) && $r['type'] != 'CNAME' && $r['type'] != 'SOA')
			{
				$info['records'][] = $r;
			}
		}
		if(count($info['records']))
		{
			$discovered[$srv] = $info;		
		}
	}
}

/* Output */

foreach($fields as $k => $v)
{
	$fields[$k] = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>TVDNS/RadioDNS Lookup Tool</title>
	</head>
	<body>
		<h1>TVDNS/RadioDNS Lookup Tool</h1>
		<?php

if(count($alerts))
{
	echo "\t\t" . '<ul class="alerts">' . "\n";
	foreach($alerts as $msg)
	{
		echo "\t\t\t" . '<li>' . $msg . '</li>' . "\n";
	}
	echo "\t\t" . '</ul>' . "\n";
}

if(strlen($fqdn))
{
	echo "\t\t" . '<div class="results">' . "\n";
	echo "\t\t\t" . '<h2>Lookup results</h2>' . "\n";
	echo "\t\t\t" . '<p>Fully-qualified domain name: <code>' . htmlspecialchars($fqdn) . '</code></p>' . "\n";
	if(strlen($svc))
	{
		echo "\t\t\t" . '<p>Service URI: <code>' . htmlspecialchars($svc) . '</code></p>' . "\n";	
	}
	if($records)
	{
		dumprecs($records);
	}
	if(strlen($target))
	{
		echo "\t\t\t" . '<p>Service discovery target: <code>' . htmlspecialchars($target) . '</code></p>' . "\n";	
	}
	if(count($discovered))
	{
		echo "\t\t\t" . '<h3>Discovered services</h3>' . "\n";
		foreach($discovered as $srv => $info)
		{
			echo "\t\t\t" . '<h4>' . htmlspecialchars($info['name']) . ' (<code>' . $srv . '.' . $target . '</code>)</h4>' . "\n";
			dumprecs($info['records']);
			if($srv == '_broadcast-meta._tcp')
			{
				$params = array();
				$srv = null;
				foreach($info['records'] as $rec)
				{
					if(!isset($rec['type'])) continue;
					if($rec['type'] == 'TXT')
					{
						$px = array();
						$plist = explode(' ', $rec['txt']);
						foreach($plist as $p)
						{
							$p = trim($p);
							if(!strlen($p)) continue;
							$kv = explode('=', $p, 2);
							if(count($kv) != 2) continue;
							$px[$kv[0]] = $kv[1];
						}
						if(isset($px['txtvers']))
						{
							$params = $px;
						}
					}
					else if($rec['type'] == 'SRV')
					{
						$srv = $rec;
						break;
					}
				}
				if(count($params) && is_array($srv) && isset($params['txtvers']) && $params['txtvers'] == 1 && isset($params['path']) && strlen($params['path']))
				{
					echo "\t\t\t" . '<form method="post" action="' . htmlspecialchars($query_string, ENT_QUOTES, 'UTF-8') . '">' . "\n";
					echo "\t\t\t\t" . '<dl>' . "\n";
					echo "\t\t\t\t\t" . '<dt><label for="uris">Enter query URIs, one per line:</label></dt>' . "\n";
					echo "\t\t\t\t\t" . '<dd><textarea name="bm:uris" id="uris" cols="60" rows="8">' . htmlspecialchars($fields['bm:uris']) . '</textarea></dd>' . "\n";
					echo "\t\t\t\t" . '</dd>' . "\n";
					echo "\t\t\t\t" . '<input type="submit" name="bm:go" value="Query">' . "\n";	
					echo "\t\t\t" . '</form>' . "\n";
					if(!empty($_POST['bm:go']))
					{
						echo "\t\t\t" . '<ul>';
						echo "\t\t\t\t" . '<li>Connecting to <code>http://' . $srv['target'] . ':' . $srv['port'] . '</code></li>';
						$ch = curl_init('http://' . $srv['target'] . ':' . $srv['port']);
						echo "\t\t\t\t" . '<li>Setting <code>Host</code> to <code>' . $fqdn . '</code></li>' . "\n";
						curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $fqdn));
						$path = $params['path'];
						if(substr($path, 0, 1) != '/') $path = '/' . $path;
						$path .= (strpos('?', $path) === false ? '?' : '&');
						$uri = explode(' ', str_replace("\n", ' ', $fields['bm:uris']));
						foreach($uri as $u)
						{
							$u = trim($u);
							if(!strlen($u)) continue;
							$path .= 'uri[]=' . urlencode($u) . '&';
						}
						echo "\t\t\t\t" . '<li>Requesting <code>' . htmlspecialchars($path, ENT_NOQUOTES, 'UTF-8') . '</code></li>' . "\n";
						curl_setopt($ch, CURLOPT_URL, 'http://' . $srv['target'] . ':' . $srv['port'] . $path);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_NOBODY, true);						
						echo "\t\t\t" . '</ul>';
						@flush();
						if(false === ($r = curl_exec($ch)))
						{
							echo "\t\t\t" . '<p>' . htmlspecialchars(curl_error($ch)) . '</p>' . "\n";
						}
						else
						{
							$info = curl_getinfo($ch);
							echo "\t\t\t" . '<p><code>' . str_replace("\n", '<br>', htmlspecialchars($r)) . '</code></p>' . "\n";
						}

					}
				}
				else
				{
					echo "\t\t\t" . '<ul>';
					if(count($params))
					{
						if(isset($params['txtvers']))
						{
							if($params['txtvers'] != 1)
							{
								echo "\t\t\t\t" . '<li>TXT record txtvers value is invalid (should be 1)</li>' . "\n";
							}
						}
						else
						{
							echo "\t\t\t\t" . '<li>TXT record txtvers value is missing</li>' . "\n";
						}
					}
					echo "\t\t\t" . '</ul>';
				}
			}
		}
	}
	else
	{
		echo "\t\t\t" . '<p>No known services discovered.</p>' . "\n";
	}
	echo "\t\t" . '</div>' . "\n";
	echo "\t\t" . '<hr>' . "\n";
}

		?>
		<form method="get" action="">
			<h2>DVB</h2>
			<input type="hidden" name="kind" value="dvb">
			<p>All identifiers should be entered in hexadecimal.</p>
			<?php			
			input('original_network_id');
			input('transport_stream_id');
			input('service_id');
			input('network_id');
			input('suffix', 'Suffix:', 'text', 'field', 'tvdns.net');
			?>
			<input type="submit" value="Go">
		</form>
		<hr>
		<form method="get" action="">
			<h2>VHF/FM</h2>
			<input type="hidden" name="kind" value="fm">
			<p>The frequency value should be an integer, in units of 10kHz (e.g., 97MHz = 9700)</p>
			<?php		
			input('freq', 'Frequency:');
			input('pi', 'RDS Programme Identification code:');
			input('country', 'ISO 3166-1 alpha-2 country code, or RDS Extended Country Code:');
			?>
			<input type="submit" value="Go">
		</form>
	</body>
</html>
<?php

function input($name, $label = null, $type = 'text', $class = 'field', $value = null)
{
	global $fields, $invalid;
	
	if(!strlen($label))
	{
		$label = $name . ':';
	}
	if(in_array($name, $invalid))
	{
		$class .= ' error';
	}
	if(strlen($fields[$name]))
	{
		$value = $fields[$name];
	}
	else
	{
		$value = htmlspecialchars($value);
	}
	echo "\t\t\t" . '<dl class="' . $class . '" id="f-' . $name . '">' . "\n";
	echo "\t\t\t\t" . '<dt><label for="' . $name . '">' . $label . '</label></dt>' . "\n";
	echo "\t\t\t\t" . '<dd><input type="' . $type . '" name="' . $name . '" id="' . $name . '" value="' . $value . '"></dd>' . "\n";
	echo "\t\t\t" . '</dl>' . "\n";
}

function dumprecs($records)
{
	echo "\t\t\t" . '<div class="dns-records">' . "\n";
	foreach($records as $rr)
	{
		if(!isset($rr['type']))
		{
			continue;
		}
		echo "\t\t\t\t" . '<p class="dns-record"><pre>' . sprintf("%-40s %-6s %s %-8s\t", $rr['host'] . '.', $rr['ttl'], $rr['class'], $rr['type']);
		switch($rr['type'])
		{
			case 'A':
				echo $rr['ip'];
				break;
			case 'AAAA':
				echo $rr['ipv6'];
				break;
			case 'MX':
				echo $rr['pri'] . " " . $rr['target'];
				break;
			case 'CNAME':
			case 'NS':
			case 'DNAME':
			case 'PTR':
				echo $rr['target'] . '.';
				break;
			case 'TXT':
				echo '"' . addslashes($rr['txt']) . '"';
				break;
			case 'SRV':
				echo $rr['pri'] . " " . $rr['weight'] . " " . $rr['target'] . ". " . $rr['port'];
				break;
		}
		echo '</pre></p>' . "\n";
	}
	echo "\t\t\t" . '</div>' . "\n";	
}


