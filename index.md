---
title: Welcome to Project Baird
layout: default
---

Project Baird is a [collaborative effort](/getting-involved/)
to create a set of open-ended, device-agnostic, platform-neutral,
cross-broadcaster specifications for delivering different kinds of content
to digital TV devices across the Internet.

Project Baird is built upon, wherever possible, existing standards and
projects. Anybody is welcome to fork and submit patches to the Project
Baird repository.

### Focus areas

1. [Service discovery](discovery/): how devices can automatically locate services relating to over-the-air broadcasts, and how users can easily subscribe to new (IP-only) services.
	* [TVDNS](discovery/tvdns/) - allowing automatic discovery of broadcasters' IP-delivered services relating over-the-air broadcasts
	* Advertising and locating IP-delivered services on a local network with [Multicast DNS and DNS-SD](discovery/bonjour/)
	* [Subscribing](discovery/subscriptions/) to services manually (i.e., user-initiated subscriptions)
2. [IP-delivered applications](applications/):
	* [Service manifests](http://wiki.github.com/nexgenta/Baird/service-manifests): a mechanism for a collection of related Atom feeds and other resources to be bundled together
	* [Content feeds](http://wiki.github.com/nexgenta/Baird/content-feeds): describing both linear and on-demand primary and secondary content to devices
	* [Electronic Programme Guide](applications/epg/): delivered via IP
	* [URI resolver](http://wiki.github.com/nexgenta/Baird/programmes-ontology-resolver): resolving a broadcast URI to a canonical URL
	* [Recommendations and sharing across the social graph](http://wiki.github.com/nexgenta/Baird/social-graph)
	* [Remote control and introspection](http://wiki.github.com/nexgenta/Baird/ip-remote-control)
3. [Web application support for TV tuners](http://wiki.github.com/nexgenta/Baird/javascript-support-for-tuners)
4. [Device profiles](http://wiki.github.com/nexgenta/Baird/device-profiles): tailoring content to different classes of device (e.g., mobile, handheld, full HD)
5. Dynamic EPG allocation: integrating linear IP-delivered content and over-the-air broadcasts into a single programme guide
6. Other supporting specifications:
	* [dvb: URI scheme](specs/draft-mcroberts-uri-dvb.html)

### Key standards and related projects

There are a number of key standards and projects upon which Project Baird is
built.

* [Atom Syndication Format](http://atompub.org/)
* [RadioDNS](http://www.radiodns.org/)
* [HTML5](http://dev.w3.org/html5/spec/Overview.html)
* [TV-Anytime](http://www.tv-anytime.org/)
* [Programmes Ontology](http://www.bbc.co.uk/ontologies/programmes/) and [Music Ontology](http://musicontology.com/)
* [NoTube](http://notube.org/)
* [URIplay](http://uriplay.org/)
* [P2P-Next](http://www.p2p-next.org/)

(Please note that there is no formal affiliation between Project Baird
and the above; they are simply those projects which are particularly important
to this effort).
