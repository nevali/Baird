<?php

if(!defined('CACHE_DIR')) define('CACHE_DIR', dirname(__FILE__) . '/cache/');
if(!defined('CACHE_TIME')) define('CACHE_TIME', '86400');

require_once(dirname(__FILE__) . '/../platform/lib/common.php');
require_once(dirname(__FILE__) . '/simplehtmldom/simple_html_dom.php');

uses('curl');

function rdf_subjects_url($url)
{	
	if(null == ($doc = cache_fetch_rdf($url)))
	{
		return null;
	}
	return rdf_subjects_string($doc, $url);
}

function rdf_subjects_string($doc, $url)
{
	$root = simplexml_load_string($doc);
	if(!is_object($root))
	{
		return null;
	}
	return rdf_subjects_dom(dom_import_simplexml($root), $url);
}

function rdf_subjects_dom($root, $url)
{
	$subjects = array();
	rdf_iterate_children($subjects, $root, $url);
	foreach($subjects as $type => $list)
	{	
		foreach($list as $k => $info)
		{
			if(($rdf = cache_fetch_rdf($info['uri'][0])))
			{
				$xml = simplexml_load_string($rdf);
				if(is_object($xml))
				{
					rdf_find_info($subjects[$type][$k], dom_import_simplexml($xml), $info['uri'][0]);
				}
			}
		}
	}
	return $subjects;
}

function rdf_find_info(&$info, $root, $base)
{
	for($node = $root->firstChild; $node; $node = $node->nextSibling)
	{
		if(!($node instanceof DOMElement))
		{
			continue;
		}
		$res = $node->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'resource');
		if($node->namespaceURI == 'http://www.w3.org/2000/01/rdf-schema#' && $node->localName == 'label')
		{
			$info['label'] = $node->textContent;
		}
		else if($node->namespaceURI == 'http://www.w3.org/2002/07/owl#' && $node->localName == 'sameAs' && strlen($res))
		{
			if(!in_array($res, $info['uri']))
			{
				$info['uri'][] = $res;
			}
		}
		rdf_find_info($info, $node, $base);
	}
}

function rdf_iterate_children(&$subjects, $root, $base)
{
	for($node = $root->firstChild; $node; $node = $node->nextSibling)
	{
		if(!($node instanceof DOMElement))
		{
			continue;
		}
		$res = $node->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'resource');
		if(strlen($res))
		{
			$type = $node->namespaceURI . $node->localName;
			$subjects[$type][]['uri'] = array(merge_urls($res, $base));
		}
		rdf_iterate_children($subjects, $node, $base);
	}
}

function cache_fetch_rdf($url)
{
	$ct = null;	
	if(null == ($doc = cache_fetch($url, $ct, 'application/rdf+xml')))
	{
		echo "No document of any kind found at " . _e($url) . "\n";
		return null;
	}
	if($ct == 'text/html')
	{
		$html = new simple_html_dom();
		$html->load($doc);
		$links = array();
		foreach($html->find('link') as $link)
		{
			$l = array(
				'rel' => @$link->attr['rel'],
				'type' => @$link->attr['type'],
				'href' => @$link->attr['href'],
				);
			if(strpos(' ' . $l['rel'] . ' ', ' alternate ') === false)
			{
				continue;
			}
			if(!strcmp($l['type'], 'application/rdf+xml'))
			{
				$links['rdf'] = $l;
			}
			$links[] = $l;
		}
		if(isset($links['rdf']))
		{
			if(null == ($href = merge_urls($links['rdf']['href'], $url)))
			{
				return null;
			}
		}
		else
		{
			if(false !== ($p = strrpos($url, '#')))
			{
				$href = substr($url, 0, $p) . '.rdf';
			}
			else
			{				   
				$href = $url . '.rdf';
			}
		}
		$doc = cache_fetch($href, $ct, 'application/xml+rdf');
	}
	if($ct == 'application/rdf+xml')
	{
		return $doc;
	}
	return null;
}

function merge_urls($url, $base, $onlyIfNonempty = false)
{
	$url = strval($url);
	$base = strval($base);
	if(!strlen($url) && $onlyIfNonempty)
	{
		return $url;
	}
	if(!strlen($base))
	{
		return $url;
	}
	$url = parse_url($url);
	$base = parse_url($base);
	if(!isset($url['scheme']) || !isset($url['host']))
	{
		$url['scheme'] = $base['scheme'];
		$url['host'] = $base['host'];
		if(isset($base['port'])) $url['port'] = $base['port'];
	}
	return $url['scheme'] . '://' . $url['host'] . (isset($url['port']) ? ':' . $url['port'] : null) . $url['path'] . (isset($url['query']) ? '?' . $url['query'] : null) . (isset($url['fragment']) ? '#' . $url['fragment'] : null);
}

function cache_fetch($url, &$contentType, $accept)
{	
	$contentType = null;
	if(strncmp($url, 'http:', 5))
	{
		echo "Sorry, only http URLs are supported\n";
		return null;
	}
	if(false !== ($p = strrpos($url, '#')))
	{
		$url = substr($url, 0, $p);
	}
	$curl = new CurlCache($url);
	if(!is_array($accept))
	{
		if(strlen($accept))
		{
			$accept = array($accept);
		}
		else
		{
			$accept = array();
		}
	}
	$accept[] = '*/*';
	$curl->returnTransfer = true;
	$curl->followLocation = true;
//		$curl->fetchHeaders = true;
	$curl->headers = array('Accept: ' . implode(',', $accept));
	$buf = $curl->exec();
	$info = $curl->info;
	$c = explode(';', $info['content_type']);
	$contentType = $c[0];	
	return strval($buf);
}

