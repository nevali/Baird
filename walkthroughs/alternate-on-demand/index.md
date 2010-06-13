---
title: Determining whether a programme is or will be available for on-demand viewing
layout: default
---

1. Construct a [TVDNS](/discovery/tvdns) or RadioDNS domain name for the
current service

2. Query DNS for the [<code>content-manifest</code>](http://wiki.github.com/nexgenta/Baird/service-manifests)
application

3. Download the manifest Atom feed and locate the feed related to the current service

4. Download the content feed for the current service

5. Locate the <code>atom:entry</code> related to the current programme, using
<code>atom:link</code> elements specifying <code>rel="alternate"</code> with an
<code>href</code> attribute containing the current programme’s <code>crid://</code> URI
or some platform-specific URI (e.g., a <code>dvb://</code> URL).

6. Examine the <code>atom:link</code> elements within the entry for those which specify
<code>rel="alternate http://projectbaird.com/ns/demand"</code> (or equivalent, depending
upon Atom’s parsing rules)

7. Filter those <code>atom:link</code> elements matching the above by their <code>type</code>
(based upon those content types supported by the device) and
<code>baird:dtend</code> attributes, removing any which have <code>type</code>
values indicating unsupported content or <code>baird:dtend</code> values
indicating that the availability window has already passed.

8. If any <code>atom:link</code> elements remain:
	* If there are any whose <code>baird:dtstart</code> attribute is in the past
	or absent, the on-demand window is currently in progress
	* If there are any whose <code>baird:dtstart</code> attribute is in the future,
	the on-demand window is in the future

9. [Indicate on-demand availability to the user](http://emberapp.com/nevali/collections/nxtv-stb-mock-ups/nevali:epg-viewing-programme-details/)

Notes:

1. Broadcaster prerequisites:
	* TVDNS domains for the services it broadcasts are configured and registered with the
	TVDNS administrative body or hierarchical parent (e.g., platform operator), as appropriate
	* <code>SRV</code> and <code>TXT</code> records for the feed manifest have been published
	* Properly-populated feed manifest and service feeds are available
2. See [http://projectbaird.com/ns/](/ns/) for a description of element,
attribute and link relation extensions
3. See [the device profiles](http://wiki.github.com/nexgenta/Baird/device-profiles)
for information on anticipated content type support on different devices
