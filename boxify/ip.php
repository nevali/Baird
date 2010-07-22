<?php

require_once(dirname(__FILE__) . '/channel.php');
require_once(dirname(__FILE__) . '/ip/data.php');

class IPChannel extends Channel
{	
	protected $principalURL;
	
	public function __construct($subject, $principalURL, $serviceClass = 'linear')
	{
		$this->kind = 'ip';
		$this->callsign = $this->displayName = $subject;
		$this->serviceClass = $serviceClass;
		$this->principalURL = $principalURL;
		$this->rdns = RadioDNS::initWithURL($principalURL);
		$this->uri = array('dns:' . $this->rdns->fqdn);
		$this->lookupURL = sprintf('/lookup/?kind=ip&url=%s', urlencode($principalURL));
		$this->hasOtaEpg = false;
		parent::__construct();
	}
}

abstract class IP
{
	public static function addChannelFromXRD(ChannelListing $listing, XRD $xrd)
	{
		$chan = null;
		if(isset($xrd->{'http://projectbaird.com/ns/serviceClass'}))
		{
			if($xrd->{'http://projectbaird.com/ns/serviceClass'}[0] == 'interactive' ||
				$xrd->{'http://projectbaird.com/ns/serviceClass'}[0] == 'demand')
			{
				if(isset($xrd->links['self']))
				{
					$chan = new IPChannel($xrd->subject, $xrd->links['self'][0]->href, $xrd->{'http://projectbaird.com/ns/serviceClass'}[0]);
					foreach($xrd->links['self'] as $link)
					{
						$chan->addSource($link->href, $link->type, $link->rel, $link->media, $link->delivery);
					}
				}
			}
		}
		if(isset($xrd->links['http://purl.org/ontology/po/IPStream']))
		{
			if(!$chan)
			{
				$chan = new IPChannel($xrd->subject, $xrd->links['http://purl.org/ontology/po/IPStream'][0]->href);
			}
			foreach($xrd->links['http://purl.org/ontology/po/IPStream'] as $link)
			{
				$chan->addSource($link->href, $link->type, $link->rel, $link->media, $link->delivery);
			}
		}
		if($chan)
		{
			$chan->attachXRD($xrd);
			$listing->addChannel($chan);
		}
	}
}
