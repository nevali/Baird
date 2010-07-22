<?php

require_once(dirname(__FILE__) . '/channel.php');
require_once(dirname(__FILE__) . '/fm/data.php');

class FMChannel extends Channel
{
	public $ecc;
	public $pi;
	public $freq;
	public $region;
	
	public function __construct($ecc, $pi, $freq, $region = null)
	{
		$this->kind = 'fm';
		$this->ecc = $ecc;
		$this->pi = $pi;
		$this->freq = $freq;
		$this->region = $region;
		$this->rdns = RadioDNS::initWithFM($ecc, $pi, $freq);
		$this->uri = array('dns:' . $this->rdns->fqdn);
		$this->lookupURL = sprintf('/lookup/?kind=fm&country=%03x&pi=%04x&freq=%05d', $ecc, $pi, $freq);
		parent::__construct();
	}
}

abstract class FM
{
	public static function addChannelsFromSource(&$listing, $country, $region, $kind = 'fm')
	{
		global $platform;
		
		foreach($platform[$kind]['ecc'][$country]['region'][$region]['pi'] as $pi => $freqs)
		{
			foreach($freqs['freq'] as $freq => $channel)
			{
				$chan = new FMChannel(intval(strval($country), 16), intval(strval($pi), 16), intval($freq), $region);
				$chan->available = true;
				$chan->callsign = $channel['ps'];
				if(strlen($channel['name']))
				{
					$chan->displayName = $channel['name'];
				}
				$chan->lcn = number_format($freq / 100, 2);
				$chan->audio = true;
				$listing->addChannel($chan);
			}
		}
	}
}
