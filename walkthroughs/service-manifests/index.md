---
title: Collating a list of all services provided by an over-the-air broadcaster
layout: default
---

1. Construct a [TVDNS](/discovery/tvdns) or RadioDNS domain name for the
current service

2. Query DNS for the [<code>content-manifest</code>](http://wiki.github.com/nexgenta/Baird/service-manifests)
application

3. Download the manifest Atom feed

4. Each linked feed relates to a service; those which are related to a known
over-the-air service can be added immediately to a list of broadcaster’s services

5. For the remainder, download each feed.

6. For each feed, examine the <code>atom:link</code> entries which are children of
the root <code>atom:feed</code> element. If:
	* <code>rel="alternate http://projectbaird.com/ns/demand"</code> is specified, or
	* <code>rel="alternate http://projectbaird.com/ns/linear"</code> is specified; and if
	* the <code>type</code> attribute contains a MIME type supported by the device; and if
	* (if present) the <code>le:media</code> attribute matches the device type and constraints; then
	* add the service described by the feed to the list of broadcaster’s services.

7. If no match was made by step 6, then if an <code>atom:entry</code> is present which contains
an <code>atom:link</code> element where:
	* <code>rel="alternate http://projectbaird.com/ns/demand"</code> is specified, or
	* <code>rel="alternate http://projectbaird.com/ns/linear"</code> is specified; and if
	* the <code>type</code> attribute contains a MIME type supported by the device; and if
	* (if present) the <code>le:media</code> attribute matches the device type and constraints; then
	* add the service described by the feed to the list of broadcaster’s services.

8. Present the list to the user

### Notes

1. Broadcaster prerequisites:
	* TVDNS domains for the services it broadcasts are configured and registered with the
	TVDNS administrative body or hierarchical parent (e.g., platform operator), as appropriate
	* <code>SRV</code> and <code>TXT</code> records for the feed manifest have been published
	* Properly-populated feed manifest and service feeds are available
2. See [http://projectbaird.com/ns/](/ns/) for a description of element,
attribute and link relation extensions
3. See [the device profiles](http://wiki.github.com/nexgenta/Baird/device-profiles)
for information on anticipated content type support on different devices
4. See [Atom Link Extensions](http://tools.ietf.org/html/draft-snell-atompub-link-extensions-06) for information on the <code>le:media</code> attribute.
5. Media queries are defined by the Atom Link Extensions specification to match those specified [by CSS3](http://www.w3.org/TR/css3-mediaqueries/)
6. The collated list of services will in many cases include a combination of
linear-only, demand-only and hybrid services, in part depending upon which
might be available over-the-air.

### Example scenario

A broadcaster has a set of linear and on-demand services. Its on-demand services are
delivered solely via IP and offers catch-up programming, as well as some exclusive
content. Of the linear services, only a subset may be available to a user of a given
platform.

Thus, if the broadcaster has seven linear channels, but the user can only access four
of them over-the-air, then they would be able to access at least some of the programmes
from the remaining three provided they were described as being available for either
linear or on-demand access via IP. If the feeds for any of those three channels contained
no information relating either the service as a whole or any of the programmes
it describes, the service would not appear in the list presented to the user.
