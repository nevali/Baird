<?php

$services = array(
	'http://www.bbc.co.uk/services/bbcone#service' => array(
		'name' => 'bbcone',
		'children' => array(
			'http://www.bbc.co.uk/services/bbcone/west#service' => array(
				'name' => 'west',
				'dvb' => array(
					array('n' => '3098', 's' => '1041', 't' => '1041', 'o' => '233a'),
				),
			),
			'http://www.bbc.co.uk/services/bbcone/south_west#service' => array(
				'name' => 'south_west',
			),
			'http://www.bbc.co.uk/services/bbcone/london#service' => array(
				'name' => 'london',
			),
			'http://www.bbc.co.uk/services/bbcone/west_midlands#service' => array(
				'name' => 'west_midlands',
			),
			'http://www.bbc.co.uk/services/bbcone/east_midlands#service' => array(
				'name' => 'east_midlands',
			),
			'http://www.bbc.co.uk/services/bbcone/east#service' => array(
				'name' => 'east',
			),
			'http://www.bbc.co.uk/services/bbcone/north_west#service' => array(
				'name' => 'north_west',
			),
			'http://www.bbc.co.uk/services/bbcone/north_east#service' => array(
				'name' => 'north_east',
			),
			'http://www.bbc.co.uk/services/bbcone/yorkshire#service' => array(
				'name' => 'yorkshire',
			),
			'http://www.bbc.co.uk/services/bbcone/oxford#service' => array(
				'name' => 'oxford',
			),
			'http://www.bbc.co.uk/services/bbcone/south_east#service' => array(
				'name' => 'south_east',
			),
			'http://www.bbc.co.uk/services/bbcone/channel_islands#service' => array(
				'name' => 'channel_islands',
			),
			'http://www.bbc.co.uk/services/bbcone/east_yorkshire#service' => array(
				'name' => 'east_yorkshire',
			),
			'http://www.bbc.co.uk/services/bbcone/scotland#service' => array(
				'name' => 'scotland',
			),
			'http://www.bbc.co.uk/services/bbcone/wales#service' => array(
				'name' => 'wales',
			),
			'http://www.bbc.co.uk/services/bbcone/ni#service' => array(
				'name' => 'ni',
			),
			'http://www.bbc.co.uk/services/bbcone/south#service' => array(
				'name' => 'south',
			),
			'http://www.bbc.co.uk/services/bbcone/cambridge#service' => array(
				'name' => 'cambridge',
			),
		),
	),
	'http://www.bbc.co.uk/services/bbctwo#service' => array(
		'name' => 'bbctwo',
		'children' => array(
			'http://www.bbc.co.uk/services/bbctwo/england#service' => array(
				'name' => 'england',
				'dvb' => array(
				      array('n' => '3098', 's' => '10bf', 't' => '1041', 'o' => '233a'),
				),
			),
			'http://www.bbc.co.uk/services/bbctwo/scotland#service' => array(
				'name' => 'scotland',
			),
			'http://www.bbc.co.uk/services/bbctwo/ni#service' => array(
				'name' => 'ni',
			),
			'http://www.bbc.co.uk/services/bbctwo/wales#service' => array(
				'name' => 'wales',
			),
		),
	),
	'http://www.bbc.co.uk/services/bbcthree#service' => array(
		'name' => 'bbcthree',
		'dvb' => array(
		      array('n' => '3098', 's' => '10c0', 't' => '1041', 'o' => '233a'),
		),
	),
	'http://www.bbc.co.uk/services/bbcnews#service' => array(
		'name' => 'bbcnews',
		'dvb' => array(
		      array('n' => '3098', 's' => '1100', 't' => '1041', 'o' => '233a'),
		),
	),
	'http://www.bbc.co.uk/services/bbcredbutton#service' => array(
		'name' => 'bbcredbutton',
		'dvb' => array(
		      array('n' => '3098', 's' => '1140', 't' => '1041', 'o' => '233a'),
		),
	),
	'http://www.bbc.co.uk/services/bbcfour#service' => array(
		'name' => 'bbcfour',
		'dvb' => array(
		      array('n' => '3098', 's' => '11c0', 't' => '1041', 'o' => '233a'),
		),
	),
	'http://www.bbc.co.uk/services/cbbc#service' => array(
		'name' => 'cbbc',
		'dvb' => array(
		      array('n' => '3098', 's' => '1200', 't' => '1041', 'o' => '233a'),
		),
	),
	'http://www.bbc.co.uk/services/bbcparliament#service' => array(
		'name' => 'bbcparliament',
		'dvb' => array(
		      array('n' => '3098', 's' => '1280', 't' => '1041', 'o' => '233a'),
		),
	),
	'http://www.bbc.co.uk/services/cbeebies#service' => array(
		'name' => 'cbeebies',
	),
	'http://www.bbc.co.uk/services#service' => array(
		'name' => 'bbctv',
	),
);
