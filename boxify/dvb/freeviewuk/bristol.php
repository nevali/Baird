<?php

$platform['dvb']['onid']['233a']['nid']['3098'] = array(
	'name' => 'Bristol (Mendip; post-DSO)',
	'tsid' => array(
		'1041' => array('name' => 'PSB1/BBC A', 'sid' => array()),
		'200c' => array('name' => 'PSB2/D3&4', 'sid' => array()),
		/* xxxx => array('name' => 'PSB3/BBC B', 'sid' => array()), */
		'3002' => array('name' => 'COM4/SDN', 'sid' => array()),
		'5000' => array('name' => 'COM5/ARQ A', 'sid' => array()),
		'6000' => array('name' => 'COM6/ARQ B', 'sid' => array()),
	),
);

/*
SELECT DISTINCT CONCAT("$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['", lcase(hex(pat_tsid)), "']['sid']['", lcase(hex(service_id)), "'] = array('name' => '", callsign, "', 'lcn' => ", chan_num, ", 'encrypted' => ", is_encrypted, ", 'data' => ", is_data_service, ", 'audio' => ", is_audio_service, ", 'authority' => '", default_authority, "');") FROM channelscan_channel ORDER BY pat_tsid;
*/

$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['11c0'] = array('name' => 'BBC FOUR', 'lcn' => 9, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1700'] = array('name' => 'BBC 1Xtra', 'lcn' => 701, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1200'] = array('name' => 'CBBC Channel', 'lcn' => 70, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1740'] = array('name' => 'BBC Asian Net.', 'lcn' => 709, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1240'] = array('name' => 'CBeebies', 'lcn' => 71, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1780'] = array('name' => 'BBC World Sv.', 'lcn' => 710, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1041'] = array('name' => 'BBC ONE', 'lcn' => 1, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1280'] = array('name' => 'BBC Parliament', 'lcn' => 81, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1a40'] = array('name' => 'BBC Radio 1', 'lcn' => 700, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['10bf'] = array('name' => 'BBC TWO', 'lcn' => 2, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1600'] = array('name' => 'BBC R5L', 'lcn' => 705, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1a80'] = array('name' => 'BBC Radio 2', 'lcn' => 702, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['10c0'] = array('name' => 'BBC THREE', 'lcn' => 7, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1640'] = array('name' => 'BBC R5SX', 'lcn' => 706, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1ac0'] = array('name' => 'BBC Radio 3', 'lcn' => 703, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1100'] = array('name' => 'BBC NEWS', 'lcn' => 80, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1680'] = array('name' => 'BBC 6 Music', 'lcn' => 707, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1b00'] = array('name' => 'BBC Radio 4', 'lcn' => 704, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1140'] = array('name' => 'BBC Red Button', 'lcn' => 105, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['16c0'] = array('name' => 'BBC Radio 7', 'lcn' => 708, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['1041']['sid']['1c00'] = array('name' => '301', 'lcn' => 301, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'fp.bbc.co.uk');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['204c'] = array('name' => 'ITV1', 'lcn' => 3, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.itv.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['2179'] = array('name' => 'Teletext', 'lcn' => 100, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['2085'] = array('name' => 'ITV2', 'lcn' => 6, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.itv.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['2180'] = array('name' => 'Directgov', 'lcn' => 106, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['20b0'] = array('name' => 'ITV2 +1', 'lcn' => 33, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.itv.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['2181'] = array('name' => 'Gay Rabbit', 'lcn' => 107, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['20c0'] = array('name' => 'Channel 4', 'lcn' => 4, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.channel4.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['21bc'] = array('name' => 'Rabbit', 'lcn' => 102, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['20fa'] = array('name' => 'More 4', 'lcn' => 14, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.channel4.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['2244'] = array('name' => 'Heart', 'lcn' => 728, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['2100'] = array('name' => 'E4', 'lcn' => 28, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.channel4.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['2104'] = array('name' => 'Channel 4+1', 'lcn' => 13, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.channel4.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['200c']['sid']['2134'] = array('name' => 'FIVE', 'lcn' => 5, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.five.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3280'] = array('name' => 'FIVER', 'lcn' => 30, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.five.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3900'] = array('name' => 'Smash Hits!', 'lcn' => 712, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3d20'] = array('name' => 'CNN', 'lcn' => 84, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3fa0'] = array('name' => 'TOPUP Anytime3', 'lcn' => 309, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['32c0'] = array('name' => 'FIVE USA', 'lcn' => 31, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.five.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['39c0'] = array('name' => 'Ttext Holidays', 'lcn' => 101, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3dc0'] = array('name' => 'Teachers TV', 'lcn' => 88, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3340'] = array('name' => 'QVC', 'lcn' => 16, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['39e0'] = array('name' => '1-2-1 Dating', 'lcn' => 104, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3ea0'] = array('name' => 'CITV', 'lcn' => 72, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.itv.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['37c0'] = array('name' => 'bid tv', 'lcn' => 23, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3a20'] = array('name' => 'MOBILIZER', 'lcn' => 109, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3eb0'] = array('name' => 'ITV3', 'lcn' => 10, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.itv.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['37e0'] = array('name' => 'Virgin1', 'lcn' => 20, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3a80'] = array('name' => 'Home', 'lcn' => 26, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3ee0'] = array('name' => 'ESPN', 'lcn' => 34, 'encrypted' => 1, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3820'] = array('name' => 'PARTYLAND', 'lcn' => 97, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3b80'] = array('name' => 'Television X', 'lcn' => 93, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3f00'] = array('name' => 'TeletextCasino', 'lcn' => 103, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['38a2'] = array('name' => 'QUEST', 'lcn' => 38, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3ba0'] = array('name' => 'TMTV', 'lcn' => 98, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3f60'] = array('name' => 'TOPUP Anytime1', 'lcn' => 307, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['38e0'] = array('name' => 'SuperCasino', 'lcn' => 39, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3cc0'] = array('name' => 'G.O.L.D.', 'lcn' => 17, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['3002']['sid']['3f80'] = array('name' => 'TOPUP Anytime2', 'lcn' => 308, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5700'] = array('name' => 'Dave', 'lcn' => 19, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5cc0'] = array('name' => 'TROVE', 'lcn' => 306, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5740'] = array('name' => 'E4+1', 'lcn' => 29, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.channel4.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5d00'] = array('name' => 'Ideal Extra', 'lcn' => 41, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5780'] = array('name' => 'smile TV3', 'lcn' => 95, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5d40'] = array('name' => 'TV News 2', 'lcn' => 90, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['57c0'] = array('name' => 'price-drop tv', 'lcn' => 37, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5840'] = array('name' => 'talkSPORT', 'lcn' => 723, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'www.talksport.net');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5640'] = array('name' => 'Sky News', 'lcn' => 82, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.sky.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['59c0'] = array('name' => 'Premier Radio', 'lcn' => 725, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5680'] = array('name' => 'Sky Spts News', 'lcn' => 83, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.sky.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5a40'] = array('name' => 'Absolute Radio', 'lcn' => 727, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['56c0'] = array('name' => 'SKY THREE', 'lcn' => 11, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.sky.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['5000']['sid']['5c80'] = array('name' => 'Sky Text', 'lcn' => 108, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6640'] = array('name' => 'Kiss', 'lcn' => 713, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6a00'] = array('name' => 'Film4', 'lcn' => 15, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.channel4.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6cc0'] = array('name' => 'Rocks & Co', 'lcn' => 40, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6680'] = array('name' => 'heat', 'lcn' => 714, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6b00'] = array('name' => 'Dave ja vu', 'lcn' => 25, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6d00'] = array('name' => 'Babestation', 'lcn' => 96, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['66c0'] = array('name' => 'Kerrang!', 'lcn' => 722, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6b40'] = array('name' => 'Russia Today', 'lcn' => 85, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6d80'] = array('name' => 'ITV4', 'lcn' => 24, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'www.itv.com');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6440'] = array('name' => '4Music', 'lcn' => 18, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6700'] = array('name' => 'SMOOTH RADIO', 'lcn' => 718, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6b80'] = array('name' => 'smile TV2', 'lcn' => 94, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6480'] = array('name' => 'VIVA', 'lcn' => 21, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['67c0'] = array('name' => 'The Hits Radio', 'lcn' => 711, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6bc0'] = array('name' => 'Babestation2', 'lcn' => 99, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['64c0'] = array('name' => 'Yesterday', 'lcn' => 12, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6800'] = array('name' => 'Magic', 'lcn' => 715, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6c00'] = array('name' => 'Big Deal', 'lcn' => 32, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6500'] = array('name' => 'Virgin1+1', 'lcn' => 35, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6840'] = array('name' => 'Q', 'lcn' => 716, 'encrypted' => 0, 'data' => 0, 'audio' => 1, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6c40'] = array('name' => 'TV News', 'lcn' => 89, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6540'] = array('name' => 'Ideal World', 'lcn' => 22, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => 'bds.tv');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6980'] = array('name' => '4TVinteractive', 'lcn' => 300, 'encrypted' => 0, 'data' => 1, 'audio' => 0, 'authority' => '');
$platform['dvb']['onid']['233a']['nid']['3098']['tsid']['6000']['sid']['6c80'] = array('name' => 'Create & Craft', 'lcn' => 36, 'encrypted' => 0, 'data' => 0, 'audio' => 0, 'authority' => '');
