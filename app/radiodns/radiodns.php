<?php

/* RadioDNS resolver */

define('RADIODNS_SUFFIX_DEFAULT', 'radiodns.org');
define('RADIODNS_SUFFIX_DVB', 'tvdns.net');

class RadioDNS
{
	public static $serviceKeys = array('radiovis', 'radioepg', 'radiotag', 'xrd', 'http', 'broadcast-meta', 'xmpp-server', 'xmpp-client');
	protected static $serviceCache = array();
	
	protected $fqdn = null;
	protected $target = null;
	protected $discovered = false;
	
	public static function initWithFQDN($fqdn)
	{
		return new RadioDNS($fqdn);
	}
	
	public static function initWithDAB($ecc, $eid, $sidmsb, $sidlsb, $scids, $appty = null, $uatype = null, $pa = null, $suffix = RADIODNS_SUFFIX_DEFAULT)
	{
		if(strlen($pa) && (strlen($uatype) || strlen($appty)))
		{
			trigger_error('RadioDNS::fqdnForDAB(): Either $pa (independent service component) or $appty and $uatype (X-PAD) can be specified', E_USER_ERROR);
			return null;
		}
		else if((strlen($uatype) || strlen($appty)) &&
				(!strlen($uatype) || strlen($appty)))
		{
			trigger_error('RadioDNS::fqdnForDAB(): For X-PAD services, both $appty and $uatype must be specified', E_USER_ERROR);
			return null;
		}
		else if(strlen($pa))
		{
			$fqdn[] = intval($pa);
		}
		else if(strlen($appty))
		{
			$fqdn[] = sprintf('%02x-%03x', $appty, $uatype);
		}
		if($scids < 0xF)
		{
			$fqdn[] = sprintf('%1x', $scids);
		}
		else
		{
			$fqdn[] = sprintf('%03x', $scids);
		}
		$sid = null;
		if($sidmsb)
		{
			$sid = sprintf('%04x', $sidmsb);
		}
		$sid .= sprintf('%04x', $sidlsb);
		$fqdn[] = $sid;
		$fqdn[] = sprintf('%04x', $eid);
		$fqdn[] = sprintf('%03x', $ecc);
		$fqdn[] = 'dab';
		$fqdn[] = $suffix;
		return new RadioDNS(implode('.', $fqdn));
	}
	
	public static function initWithDVB($onid, $tsid, $sid, $nid = 0, $suffix = RADIODNS_SUFFIX_DVB)
	{
		return new RadioDNS(sprintf('%04x.%04x.%04x.%04x.dvb.%s', $nid, $sid, $tsid, $onid, $suffix));
	}

	public static function initWithFM($ecc, $pi, $freq, $suffix = RADIODNS_SUFFIX_DEFAULT)
	{
		if(is_int($ecc))
		{
			$ecc = sprintf('%03x', $ecc);
		}
		else
		{
			$ecc = strtolower(substr($ecc, 0, 2));
		}
		return new RadioDNS(sprintf('%05d.%04x.%s.fm.%s', $freq, $pi, $ecc, $suffix));
	}
	
	public static function initWithURL($url)
	{
		try
		{
			@$info = parse_url($url);
		}
		catch(Exception $e) {}
		if(!$info || !isset($info['host']))
		{
			return null;
		}
		return new RadioDNS($info['host']);
	}
	
	protected function __construct($fqdn)
	{
		$this->fqdn = $fqdn;
	}
	
	protected function locateTarget()
	{
		$domain = $this->fqdn;
		do
		{
			$target = $domain;
			if(!($recs = dns_get_record($domain, DNS_ANY)))
			{
				break;
			}
			$domain = null;
			foreach($recs as $rr)
			{
				if(!isset($rr['type']) || $rr['type'] != 'CNAME')
				{
					continue;
				}
				$domain = $rr['target'];
			}
		}
		while($domain != null);
		$this->target = $target;
	}
	
	public function discover($service = null, $serviceProto = 'tcp')
	{
		if(!$this->target)
		{
			$this->locateTarget();
		}
		if(!isset(self::$serviceCache[$this->target]))
		{
			self::$serviceCache[$this->target] = array();
		}
		if(strlen($service))
		{
			$service = array($service);
		}
		else
		{
			$service = self::$serviceKeys;
			$this->discovered = true;
		}
		if(strlen($serviceProto))
		{
			$serviceProto = array($serviceProto);
		}
		else
		{
			$serviceProto = array('tcp', 'udp');
		}
		$serviceList = array();
		foreach($service as $srv)
		{
			foreach($serviceProto as $proto)
			{
				$key = '_' . $srv . '._' . $proto;
				if(isset(self::$serviceCache[$this->target][$key]))
				{
					$serviceList[$key] = self::$serviceCache[$this->target][$key];
					continue;
				}
				$info = array('srv' => array(), 'txt' => array(), 'params' => array());
				if(!($recs = dns_get_record('_' . $srv . '._' . $proto . '.' . $this->target, DNS_ANY)))
				{
					continue;
				}
				foreach($recs as $rr)
				{
					if(!isset($rr['type'])) continue;
					if($rr['type'] == 'SRV')
					{
						$info['srv'][] = $rr;
						continue;
					}
					if($rr['type'] == 'TXT')
					{
						$info['txt'][] = $rr['txt'];
					}
				}
				if(count($info['srv']))
				{
					if(count($info['txt']))
					{
						foreach($info['txt'] as $text)
						{
							$params = explode(' ', $text);
							foreach($params as $p)
							{
								$kv = explode('=', $p, 2);
								if(count($kv) != 2) continue;
								$info['params'][$kv[0]] = urldecode($kv[1]);
							}
						}
					}
					$serviceList[$key] = self::$serviceCache[$this->target][$key] = $info;
				}
			}
		}
		return $serviceList;
	}
	
	public function __get($name)
	{
		switch($name)
		{
		 case 'fqdn':
			return $this->fqdn;
		 case 'target':
			if($this->target == null)
			{
				$this->locateTarget();
			}
			return $this->target;
		 case 'services':
			if($this->discovered == false)
			{
				return $this->discover();
			}
			return self::$serviceCache[$this->target];
		}
		return null;
	}
}
