<?php

class Discovery
{
	public static $services = array(
		'_radioepg._tcp' => 'RadioEPG', 
		'_radiovis._tcp' => 'RadioVIS',
		'_radiotag._tcp' => 'RadioTAG',
		'_broadcast-meta._tcp' => 'URI Resolver',
		'_xrd._tcp' => 'Service manifest',
		'_http._tcp' => 'Web page',
	);

	public $fqdn;
	public $target;
	public $discovered = array();
	
	public function __construct($fqdn)
	{
		$this->fqdn = $this->target = $fqdn;
	}
	
	public function discover()
	{
		$records = dns_get_record($this->fqdn);
		foreach($records as $rec)
		{
			if(isset($rec['type']) && $rec['type'] == 'CNAME')
			{
				$this->target = $rec['target'];
				/* Don't break - we want the last record */
			}
		}
		foreach(self::$services as $srv => $info)
		{
			if(!is_array($info))
			{
				$info = array('name' => $info);
			}
			$info['records'] = array();
			$info['fqdn'] = $this->fqdn;
			$info['target'] = $this->target;
			$recs = dns_get_record($srv . '.' . $this->target);
			foreach($recs as $r)
			{
				if(isset($r['type']) && $r['type'] != 'CNAME' && $r['type'] != 'SOA')
				{
					$info['records'][] = $r;
				}
			}
			if(count($info['records']))
			{
				$this->discovered[$srv] = $info;		
			}
		}
	}
}