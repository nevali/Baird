<?php

/* DRM & AMSS */

$fields['sid'] = null;

abstract class DRM_AMSS
{
	public static function processForm(&$fields)
	{
		global $alerts, $invalid;
		
		if(!strlen($fields['enc'])) $fields['enc'] = 'hex';
		if(!strlen($fields['suffix']))
		{
			$fields['suffix'] = 'radiodns.org';
		}
		$f = array('sid');
		foreach($f as $k)
		{
			if(!strlen($fields[$k]))
			{
				$alerts[] = $k . ' is a required field';
				continue;
			}
			if(!ctype_xdigit($fields[$k]))
			{
				$alerts[] = $k . ' must be a 24-bit ' . ($fields['enc'] == 'dec' ? 'decimal' : 'hexadecimal') . ' unsigned integer';
				continue;
			}
			if($fields['enc'] == 'dec')
			{
				if(!ctype_digit($fields[$k]))
				{
					$alerts[] = $k . ' must be a 24-bit decimal unsigned integer';
					continue;
				}
				$v = intval($fields[$k]);
			}
			else
			{
				$v = intval($fields[$k], 16);
			}
			if($v < 0 || $v > 16777215)
			{
				$alerts[] = $f . ' must be a 24-bit ' . ($fields['enc'] == 'dec' ? 'decimal' : 'hexadecimal') . ' unsigned integer';
				continue;			
			}
			$fqdn[] = sprintf('%06x', $v);
		}
		if(count($alerts))
		{
			return;
		}
		$fqdn[] = $fields['kind'];
		$fqdn[] = $fields['suffix'];
		return array('svc' => null, 'fqdn' => $fqdn);
	}
}