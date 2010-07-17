<?php

$fields['url'] = null;

abstract class IPStream
{
	public static function processForm(&$fields)
	{
		global $alerts, $invalid;
		
		if(!strlen($fields['url']))
		{
			$alerts[] = 'URL is a mandatory field';
			return;
		}
		try
		{
			$info = parse_url($fields['url']);
		}
		catch(Exception $e)
		{
		}
		if(isset($info['host']))
		{
			return array('svc' => $fields['url'], 'fqdn' => $info['host']);
		}
		$alerts[] = 'Unable to parse URL';
		return;
	}
}
