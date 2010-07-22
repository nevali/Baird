<?php

require_once(MODULES_ROOT . 'xrd/xrd.php');

abstract class Channel
{
	public $kind = 'unknown';
	public $serviceClass = 'linear';
	public $rdns;
	public $available;
	public $lcn;
	public $callsign;
	public $displayName;
	public $uri = array();
	public $lookupURL = null;
	public $hasOtaEpg = false;
	public $audio = false;
	public $data = false;
	public $ca = false;
	public $parent = null;
	public $broadcaster = null;
	public $xrd = null;
	
	public $sources = array();
	public $services = array();
	
	public $depiction = array();
	
	protected function __construct($fdqn = null)
	{
		if($fdqn)
		{
			$this->rnds = RadioDNS::initWithFQDN($fqdn);
		}
		if(isset($this->rdns->services['_http._tcp']))
		{
			$service = $this->rdns->services['_http._tcp'];
			if(isset($service['params']['path']))
			{
				foreach($service['srv'] as $srv)
				{
					$p = $service['params']['path'];
					if(substr($p, 0, 1) != '/') $p = '/' . $p;
					$this->services['_http._tcp'] = array('uri' => 'http://' . $srv['target'] . ($srv['port'] == 80 ? null : ':' . $srv['port']) . $p, 'host' => $srv['target']);
				}
			}
		}
		if(isset($this->rdns->services['_xrd._tcp']))
		{
			$service = $this->rdns->services['_xrd._tcp'];
			if(isset($service['params']['path']))
			{
				foreach($service['srv'] as $srv)
				{
					$p = $service['params']['path'];
					if(substr($p, 0, 1) != '/') $p = '/' . $p;
					$this->services['_xrd._tcp'] = 'http://' . $srv['target'] . ($srv['port'] == 80 ? null : ':' . $srv['port']) . $p;
				}
			}
		}
		$list = array('broadcast-meta', 'radioepg', 'radiovis', 'radiotag');
		foreach($list as $s)
		{
			$s = '_' . $s . '._tcp';
			if(isset($this->rdns->services[$s]))
			{
				$this->services[$s] = $this->rdns->services[$s];
			}
		}
	}
	
	public function addSource($uri, $type = null, $rel = 'alternate', $media = 'all', $delivery = null)
	{
		$matches = null;
		$proto = null;
		if(preg_match('/^([a-z-+]+):/', $uri, $matches))
		{
			$proto = $matches[1];
		}
		$this->uri[] = $uri;
		$this->sources[] = array(
			'proto' => $proto,
			'href' => $uri,
			'type' => $type,
			'rel' => $rel,
			'media' => $media,
			'delivery' => $delivery,
		);
	}
	
	public function matchesXRD(XRD $xrd)
	{
		$list = array('self', 'http://purl.org/ontology/po/Channel', 'http://purl.org/ontology/po/DVB', 'http://purl.org/ontology/po/IPStream', 'http://purl.org/ontology/po/FM', 'http://purl.org/ontology/po/DAB');
		foreach($list as $l)
		{
			if(isset($xrd->links[$l]))
			{
				foreach($xrd->links[$l] as $link)
				{
					foreach($this->uri as $uri)
					{
						if(!strcmp($link->href, $uri))
						{
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
	public function attachXRD(XRD $xrd)
	{
		$this->xrd = $xrd;
		$this->parent = $xrd->parent;
		$this->broadcaster = $xrd->broadcaster;
		$xrd->channels[] = $this->lcn;
		
		/* Update channel data from XRD */
		$this->displayName = $xrd->label($this->displayName);
		if(isset($xrd->links['http://xmlns.com/foaf/0.1/depiction']))
		{
			$this->depiction = $xrd->links['http://xmlns.com/foaf/0.1/depiction'];
		}
		if(isset($xrd->{'urn:tva:metadata:2005:ServiceGenre'}) && in_array('urn:tva:metadata:cs:MediaTypeCS:2005:7.1.1', $xrd->{'urn:tva:metadata:2005:ServiceGenre'}))
		{
			$this->audio = true;
		}
	}
}

class ChannelListing
{
	protected $channels = array();
	protected $platforms = array();
	protected $lastDynamicChannel = 900;
	protected $lastUnnumberedChannel = 9000;
	
	public function channels()
	{
		ksort($this->channels);
		return $this->channels;
	}
	
	public function replaceChannels($list)
	{
		$this->channels = $list;
	}
	
	public function addPlatform(XRD $xrd)
	{
		/* In the real world, we'd maintain a list per XRD source to avoid
		 * problems with clashes
		 */
		$this->platforms[$xrd->subject] = $xrd;
	}
	
	/* Add a new channel to the listing */
	public function addChannel(Channel $channel)
	{
		$allocateLCN = true;

		/* Additional variants of channels which are
		 * already present in the listing don't get
		 * allocated channel numbers.
		 */		
		if($channel->parent)
		{
			foreach($this->channels as $chan)
			{
				if($chan->parent && $chan->parent == $channel->parent)
				{
					$channel->lcn = null;
					$allocateLCN = false;
				}
			}
		}
		if($allocateLCN && $channel->lcn)
		{
			$allocateLCN = false;
		}
		if($allocateLCN && isset($channel->xrd->{'http://projectbaird.com/ns/serviceNumberPreference'}))
		{
			foreach($channel->xrd->{'http://projectbaird.com/ns/serviceNumberPreference'} as $pref)
			{
				$pref = explode('=', $pref, 2);
				$pref[0] = intval($pref[0]);
				if(!$pref[0]) continue;
				$key = sprintf('%04d', $pref[0]);
				if(isset($this->channels[$key]))
				{
					continue;
				}
				if(isset($pref[1]) && strlen($pref[1]) && !isset($this->platforms[$pref[1]]))
				{
					continue;
				}
				$channel->lcn = $pref[0];
				$allocateLCN = false;
				break;
			}
		}
		if($allocateLCN)
		{
			$this->lastDynamicChannel++;
			$channel->lcn = $this->lastDynamicChannel;
		}
		if($channel->lcn)
		{
			$key = sprintf('%04d', $channel->lcn);
		}
		else
		{
			$this->lastUnnumberedChannel++;
			$key = sprintf('%04d', $this->lastUnnumberedChannel);
		}
		$this->channels[$key] = $channel;
	}
	
	/* Return the channel matching the information in the specified XRD */
	public function channelMatchingXRD(XRD $xrd)
	{
		foreach($this->channels as $chan)
		{
			if($chan->matchesXRD($xrd))
			{
				return $chan;
			}
		}
		return null;
	}
	
	/* Match XRDS to channels */
	public function matchXRDS($xrds)
	{
		if($xrds instanceof XRDS)
		{
			$xrds = $xrds->xrd;
		}
		foreach($xrds as $xrd)
		{
			if(($chan = $this->channelMatchingXRD($xrd)))
			{
				$chan->attachXRD($xrd);
			}
		}		
	}
	
	/* Check if the XRD for a platform matches the services already present */
	public function platformXRDMatches(XRD $platform)
	{
		if(!empty($platform->matched))
		{
			return true;
		}
		$dvbLinks = array();
		if(isset($platform->links['http://purl.org/ontology/po/DVB']))
		{
			foreach($platform->links['http://purl.org/ontology/po/DVB'] as $link)
			{
				$matches = array();
				if(preg_match('!^dvb://([0-9a-f]{1,4})(\..*)?$!i', $link->href, $matches))
				{
					$dvbLinks[] = 'dvb://' . $matches[1] . '.';
				}
			}
		}	
		foreach($this->channels as $chan)
		{
			if(isset($chan->links['http://purl.org/ontology/po/DVB']))
			{
				foreach($chan->links['http://purl.org/ontology/po/DVB'] as $link)
				{	
					foreach($dvbLinks as $l)
					{
						if(!strncasecmp($link->href, $l, strlen($l)))
						{
							die('matched ' . $platform->subject);
							$platform->matched = true;
							return true;
						}
					}
				}
			}
		}
	}
}

class ChannelXRDS extends XRDS
{
	public function __construct()
	{
		/* We want an empty XRDS store */
		parent::__construct();
	}
	
	/* Find parents and broadcasters for XRDs */
	public function locateParents()
	{
		foreach($this->xrd as $subject => $xrd)
		{
			if(isset($xrd->links['http://purl.org/ontology/po/broadcaster'][0]))
			{
				$uri = $xrd->links['http://purl.org/ontology/po/broadcaster'][0]->href;
				if(isset($this->xrd[$uri]))
				{
					$xrd->broadcaster = $this->xrd[$uri];
				}
			}
			if(isset($xrd->links['http://purl.org/ontology/po/parent_service'][0]))
			{
				$uri = $xrd->links['http://purl.org/ontology/po/parent_service'][0]->href;
				if(isset($this->xrd[$uri]) && empty($this->xrd[$uri]->parent))
				{
					$xrd->parent = $this->xrd[$uri];
					foreach($xrd->parent->links as $rel => $links)
					{
						if(!isset($xrd->links[$rel]))
						{
							$xrd->links[$rel] = $links;
							continue;
						}
						foreach($links as $link)
						{
							$xrd->links[$rel][] = $link;
						}
					}
					$props = get_object_vars($xrd->parent);
					foreach($props as $k => $vlist)
					{
						if(isset($this->$k) && !is_array($this->$k))
						{
							continue;
						}
						if(!isset($this->$k))
						{					
							$this->$k = $vlist;
							continue;
						}
						foreach($vlist as $value)
						{
							$this->{$k}[] = $value;
						}
					}
				}
			}
		}
	}
	
	/* Perform platform-matching against the line-up */
	public function matchPlatformsAgainstListing(ChannelListing $listing)
	{
		foreach($this->xrd as $xrd)
		{
			if(isset($xrd->{'http://projectbaird.ns/platform'}))
			{
				if($listing->platformXRDMatches($xrd))
				{
					$this->addPlatform($xrd);
				}
			}
		}
	}
	
	/* Find IP-based services and create channels for them */
	public function addIPServicesToListing(ChannelListing $listing)
	{
		foreach($this->xrd as $subject => $xrd)
		{
			if(isset($xrd->channels) && count($xrd->channels))
			{
				continue;
			}
			$add = false;
			if(isset($xrd->{'http://projectbaird.com/ns/serviceClass'}))
			{
				if($xrd->{'http://projectbaird.com/ns/serviceClass'}[0] == 'interactive' ||
					$xrd->{'http://projectbaird.com/ns/serviceClass'}[0] == 'demand')
				{
					if(isset($xrd->links['self']))
					{
						$add = true;
					}
				}
			}
			if(isset($xrd->links['http://purl.org/ontology/po/IPStream']))
			{
				$add = true;
			}
			if($add)
			{
				IP::addChannelFromXRD($listing, $xrd);
			}
		}
	}
}
