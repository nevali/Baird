<?php

require_once(dirname(__FILE__) . '/../../platform/lib/common.php');
require_once(dirname(__FILE__) . '/radiodns.php');

echo "==> Capital Radio has ecc=c586, eid=c185, sid=c586\n";
$rdns = RadioDNS::initWithDAB(0xce1, 0xc185, 0, 0xc586, 0);
echo "--> FQDN is " . $rdns->fqdn . "\n";
echo "--> Target is " . $rdns->target . "\n";
print_r($rdns->services);


echo "==> BBC One (West) has onid=233a, tsid=1041, sid=1041, nid=3098\n";
$rdns = RadioDNS::initWithDVB(0x233a, 0x1041, 0x1041, 0x3098);
echo "--> FQDN is " . $rdns->fqdn . "\n";
echo "--> Target is " . $rdns->target . "\n";
print_r($rdns->services);
