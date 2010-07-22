<?php

class XRDLink
{
	public $rel;
	public $type;
	public $href;
	public $media = 'all';
	public $delivery;
	public $width;
	public $height;
	public $depth;
	public $preferredBackground;
	
	public function __construct($href = null, $type = null, $rel = null)
	{
		$this->href = $href;
		$this->type = $type;
		$this->rel = $rel;
	}
}

class XRD
{
	public $subject = null;
	public $links = array();

	public $broadcaster;
	public $parent;
	public $channels = array();


	public function label($default = null)
	{
		$label = null;
		if(isset($this->parent->{'http://www.w3.org/2000/01/rdf-schema#label'}[0]))
		{
			$label = $this->parent->{'http://www.w3.org/2000/01/rdf-schema#label'}[0] . ' ';
		}
		if(isset($this->{'http://www.w3.org/2000/01/rdf-schema#label'}[0]))
		{
			$label .= $this->{'http://www.w3.org/2000/01/rdf-schema#label'}[0];
		}
		$label = trim($label);
		if(strlen($label))
		{
			return $label;
		}
		return $default;	
	}
}

class XRDS
{
	public static $xrdCache;
	
	public $xrd = array();
	
	public static function xrdsFromURI($uri)
	{
		if(!isset(self::$xrdCache[$uri]))
		{
			self::$xrdCache[$uri] = new XRDS($uri);
		}
		return self::$xrdCache[$uri];
	}
	
	protected function __construct($uri = null)
	{
		if(strlen($uri))
		{
			$xml =  simplexml_load_file($uri);
			if(is_object($xml))
			{
				if($xml->getName() == 'XRD')
				{
					$entries = array($this->parse($xml));
				}
				else
				{
					$entries = array();
					foreach($xml->XRD as $x)
					{
						$entries[] = $this->parse($x);
					}
				}
				foreach($entries as $entry)
				{
					if(!is_object($entry) || !strlen($entry->subject)) continue;
					$this->xrd[$entry->subject] = $entry;
				}
			}
		}
	}
	
	public function mergeFrom($xrds)
	{
		if($xrds === null)
		{
			return;
		}
		if($xrds instanceof XRDS)
		{
			$xrds = $xrds->xrd;
		}
		else if($xrds instanceof XRD)
		{
			$xrds = array($xrds);
		}
		foreach($xrds as $x)
		{
			$found = false;
			foreach($this->xrd as $match)
			{
				if(!strcmp($match->subject, $x->subject))
				{
					$found = true;
					break;
				}
			}
			if(!$found)
			{
				$this->xrd[$x->subject] = $x;
			}
		}
	}
	
	protected function parse($node)
	{
		global $xrd;
		
		$entry = new XRD();
		$entry->subject = trim($node->Subject);
		foreach($node->Link as $l)
		{
			$a = $l->attributes();
			$k = trim($a->rel);
			if(!strlen($k)) continue;
			$link = new XRDLink(trim($a->href), trim($a->type), $k);
			foreach($l->Property as $prop)
			{
				$a = $prop->attributes();
				$pk = trim($a->type);
				if(!strlen($pk)) continue;
				$prop = trim($prop);
				$link->{$pk} = $prop;
				switch($pk)
				{
					case 'http://projectbaird.com/ns/media':
						$link->media = $prop;
						break;
					case 'http://projectbaird.com/ns/delivery':
						$link->delivery = $prop;
						break;
					case 'http://projectbaird.com/ns/width':
						$link->width = intval($prop);
						break;
					case 'http://projectbaird.com/ns/height':
						$link->height = intval($prop);
						break;
					case 'http://projectbaird.com/ns/depth':
						$link->depth = intval($prop);
						break;
					case 'http://projectbaird.com/ns/preferredBackground':
						$link->preferredBackground = intval($prop);
						break;
				}
			}
			if(!isset($entry->links[$k]))
			{
				$entry->links[$k] = array();
			}
			$entry->links[$k][] = $link;
		}
		foreach($node->Property as $prop)
		{
			$a = $prop->attributes();
			$k = trim($a->type);
			if(!strlen($k)) continue;
			if(!isset($entry->{$k}))
			{
				$entry->{$k} = array();
			}
			$entry->{$k}[] = trim($prop);
		}
		return $entry;
	}

}
