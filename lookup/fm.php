<?php

/* VHF/FM with RDS */

$fields['freq'] = null;
$fields['pi'] = null;
$fields['country'] = null;
$fields['multiplier'] = null;

abstract class FM
{
	public static function processForm(&$fields)
	{
		global $alerts, $invalid;

		$fields['multiplier'] = floatval($fields['multiplier']);
		if(!$fields['multiplier'])
		{
			$fields['multiplier'] = 1;
		}
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
				$v = intval(floatval($fields[$k]) * $fields['multiplier']);
				if(!$v)
				{
					$alerts[] = 'The frequency must be specified as a number (no suffix), in units of ' . ($fields['multiplier'] == 0 ? '10kHz' : '1MHz');
					continue;
				}
				$v = sprintf('%05d', $v);
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
			return;
		}
		$fqdn[] = 'fm';
		$fqdn[] = $fields['suffix'];
		return array('svc' => null, 'fqdn' => $fqdn);
	}
}
