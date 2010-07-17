<?php

$fields['ecc'] = null;
$fields['eid'] = null;
$fields['sid'] = null;
$fields['scids'] = null;
$fields['appty'] = null;
$fields['uatype'] = null;
$fields['pa'] = null;

abstract class DAB
{
	public static function processForm(&$fields)
	{
		global $alerts, $invalid;
		
		$appty = null;
		if(!strlen($fields['suffix']))
		{
			$fields['suffix'] = 'radiodns.org';
		}
		if(strlen($fields['pa']))
		{
			if(strlen($fields['appty']) || strlen($fields['uatype']))
			{
				$alerts[] = 'You can specify <strong>either</strong> a Packet Address <strong>or</strong> a X-PAD Application Type and User Application Type pair';
				return;
			}
		}
		if(strlen($fields['appty']) || strlen($fields['uatype']))
		{
			if(!strlen($fields['appty']) || !strlen($fields['uatype']))
			{
				$alerts[] = 'Both X-PAD Application Type and User Application type must both be specified for X-PAD services';
				return;
			}
		}
		if(strlen($fields['pa']))
		{
			$f = array('pa', 'scids', 'sid', 'eid', 'ecc');
		}
		else if(strlen($fields['appty']))
		{
			$f = array('appty', 'uatype', 'scids', 'sid', 'eid', 'ecc');
		}
		else
		{
			$f = array('scids', 'sid', 'eid', 'ecc');
		}
		foreach($f as $k)
		{
			if(!strlen($fields[$k]))
			{
				$alerts[] = $k . ' is a required field';
				continue;
			}
			if($k == 'pa')
			{
				if(!ctype_digit($fields[$k]))
				{
					$alerts[] = 'Packet Address must be an decimal integer between 0 and 1023';
					continue;
				}
				$v = intval($fields[$k]);
				if($v < 0 || $v > 1023)
				{
					$alerts[] = 'Packet Address must be an decimal integer between 0 and 1023';
					continue;				
				}
				$format = '%d';
			}
			else
			{
				if(!ctype_xdigit($fields[$k]))
				{
					$alerts[] = $k . ' is not a valid hexadecimal unsigned integer';
					continue;
				}
				$v = intval($fields[$k], 16);
				switch($k)
				{
					case 'ecc':
						if($v > 0xFFF)
						{
							$alerts[] = $k . ' must be a 3-digit hexadecimal unsigned integer';
							continue; 
						}
						$format = '%03x';
						break;
					case 'eid':
						if($v > 0xFFFF)
						{
							$alerts[] = $k . ' must be a 4-digit hexadecimal unsigned integer';
							continue; 
						}
						$format = '%04x';
						break;
					case 'sid':
						if(strlen($fields[$k]) > 8)
						{
							$alerts[] = $k . ' must be a 4- or 8-digit hexadecimal unsigned integer';
							continue;
						}
						if(strlen($fields[$k]) > 4)
						{
							$v = str_pad($fields[$k], 8, '0', STR_PAD_LEFT);
							$format = '%s';
						}
						else
						{
							$format = '%04x';
						}
						break;
					case 'scids':
						if($v > 0xFFF)
						{
							$alerts[] = $k . ' must be a 1- or 3-digit hexadecimal unsigned integer';
							continue; 
						}
						if($v > 0xF)
						{
							$format = '%03x';
						}
						else
						{
							$format = '%x';
						}
						break;
					case 'appty':
						if($v > 0xFF)			
						{
							$alert[] = $k . ' must be a 2-digit hexadecimal unsigned integer';
							continue;
						}
						$appty = $v;
						continue;
					case 'uatype':
						if($v > 0xFFF)			
						{
							$alert[] = $k . ' must be a 2-digit hexadecimal unsigned integer';
							continue;
						}
						$v = sprintf('%02x-%03x', $appty, $v);
						$format = '%s';
						break;
				}
			}
			$fqdn[] = sprintf($format, $v);
		}
		if(count($alerts))
		{
			return;
		}
		$fqdn[] = 'dab';
		$fqdn[] = $fields['suffix'];
		return array('svc' => null, 'fqdn' => $fqdn);
	}
}