<?php

$apps = array(
	array('namespace' => 'http://dbpedia.org/resource/',
		  'label' => 'Wikipedia',
		  'transform' => 'http://en.wikipedia.org/wiki/%b',
		  'action' => 'Find out more about %l on Wikipedia',
		),
/*	array('namespace' => 'http://dbpedia.org/resource/',
		  'label' => 'Articles',
		  'transform' => 'articles://en.wikipedia.org/wiki/%b',
		  'action' => 'Find out more about %l with Articles.app',
		  ), */
	array('namespace' => 'http://www.bbc.co.uk/nature/',
		  'label' => 'BBC Wildlife Finder',
		  'transform' => '%u',
		  'action' => 'Explore %l with BBC Wildlife Finder',
		),
	array('namespace' => 'http://www.imdb.com/',
		  'label' => 'IMDb',
		  'transform' => '%u',
		  'action' => 'Discover %l on IMDb',
		),
	);


