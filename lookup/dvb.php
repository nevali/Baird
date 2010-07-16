<?php

/* DVB SI */

$fields['original_network_id'] = null;
$fields['transport_stream_id'] = null;
$fields['service_id'] = null;
$fields['network_id'] = null;
$fields['enc'] = null;

abstract class DVB
{
	public static function processForm(&$fields)
	{
		global $alerts, $invalid;
		
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
			return;
		}
		$svc = 'dvb://' . $fqdn[3] . '.' . $fqdn[2] . '.' . $fqdn[1];
		$fqdn[] = 'dvb';
		$fqdn[] = $fields['suffix'];
		return array('svc' => $svc, 'fqdn' => $fqdn);
	}
}