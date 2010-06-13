---
title: Locating programme information for over-the-air broadcasts
layout: default
---

1. Construct a [TVDNS](/discovery/tvdns) or RadioDNS domain name for the
current service

2. Query DNS for the [<code>broadcast-meta</code>](http://wiki.github.com/nexgenta/Baird/programmes-ontology-resolver)
application

3. Connect to the resolver service and supply the current programme’s
<code>crid://</code> URI (if available), and start time according to
supplied EPG data

4. Follow the redirect to the metadata service and set the <code>Accept:</code>
request header according to the data format required

5. [Present the data to the user](http://emberapp.com/nevali/collections/nxtv-stb-mock-ups/nevali:details/)

Notes:

1. Broadcaster prerequisites:
	* TVDNS domains for the services it broadcasts are configured and registered with the
	TVDNS administrative body or hierarchical parent (e.g., platform operator), as appropriate
	* <code>SRV</code> and <code>TXT</code> records for the resolver service have been published
	* A properly-running resolver service which redirects based upon URI, TVDNS FQDN and start time
	* A properly-running metadata service
2. The same process can be carried out for [past or future programmes](http://emberapp.com/nevali/collections/nxtv-stb-mock-ups/nevali:epg-viewing-programme-details/) in
the EPG, not just that which is currently tuned to.
3. Linked screenshots — the metadata can be considerably richer than shown (they will be updated at some stage)
