---
title: Locating MHEG and HTML applications related to programmes
layout: default
---

1. Construct a [TVDNS](/discovery/tvdns) or RadioDNS domain name for the
current service

2. Query DNS for the [<code>xrd</code>](http://github.com/nexgenta/Baird/wiki/Service-manifests)
application

3. Download the manifest XRD and locate the feed related to the current service

4. Download the feed for the current service

5. Locate the <code>atom:entry</code> related to the current programme, using
<code>atom:link</code> elements specifying <code>rel="alternate"</code> with an
<code>href</code> attribute containing the current programme’s <code>crid://</code> URI
or some platform-specific URI (e.g., a <code>dvb://</code> URL).

6. Examine the <code>atom:link</code> elements within the entry for those which specify
<code>rel="related"</code> (or equivalent, depending
upon Atom’s parsing rules)

7. Filter those <code>atom:link</code> elements matching the above by their <code>type</code>
(based upon those content types supported by the device) and <code>le:media</code> (if present).
The value of the <code>type</code> attribute should be one of the following:
	* <code>text/html</code> — HTML application
	* <code>application/xhtml+xml</code> — HTML application, XHTML serialisation
	* <code>application/mheg</code> — Compiled MHEG application (see note 6 below)

8. Order the resulting set by preference based upon the available information
(type, media query) and attempt to download the preferred match and its immediate
dependencies.

9. Present the application to the user.

### Notes

1. Broadcaster prerequisites:
	* TVDNS domains for the services it broadcasts are configured and registered
	with the TVDNS administrative body or hierarchical parent (e.g., platform
	operator), as appropriate
	* <code>SRV</code> and <code>TXT</code> records for the feed manifest have
	been published
	* Properly-populated feed manifest and service feeds are available
2. See [http://projectbaird.com/ns/](/ns/) for a description of element,
attribute and link relation extensions
3. See [the device profiles](http://github.com/nexgenta/Baird/wiki/Device-profiles)
for information on anticipated content type support on different devices
4. See [Atom Link Extensions](http://tools.ietf.org/html/draft-snell-atompub-link-extensions-06)
for information on the <code>le:media</code> attribute.
5. Media queries are defined by the Atom Link Extensions specification to
match those specified [by CSS3](http://www.w3.org/TR/css3-mediaqueries/)
6. The MIME type <code>application/mheg</code> should be augmented with a
<code>profile</code> parameter, indicating the MHEG profile in use (e.g.,
<code>application/mheg;profile="uk1.06"</code>. A list of known profile
identifiers will be detailed separately.
