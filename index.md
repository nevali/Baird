---
title: Welcome to Project Baird
layout: default
section: home
---

Project Baird is a [collaborative effort](/getting-involved/)
to collate a set of open-ended specifications for delivering different kinds
of content to digital TV devices across the Internet. The specifications
are intended to be device–, broadcaster– and platform—agnostic as much
as possible.

Project Baird is built upon, wherever possible, existing standards and
projects. Anybody is welcome to fork and submit patches to the Project
Baird repository.

### Focus areas

Note: links below to wiki pages represent early drafts; names may change (and indeed
have in the past), and things may get thrown out and started again. Feel free to
contribute directly by editing the wiki pages. Links to more structured documents
are working or in-progress drafts which are being formulated based on general
consensus.

#### 1. Ancilliary service (application) discovery

This area covers mechanisms for identifying broadcast services (channels),
and for discovering IP-delivered services, or applications, which are advertised
as being associated with those channels or are otherwise available. There are
various different mechanisms available, and there is no single "right" approach.
For example, a large broadcaster who operates much of their own infrastructure
might prefer to deliver information needed for application discovery as part of
the broadcast stream, whereas a smaller broadcaster might find it logistically
less troublesome to advertise solely via TVDNS. A broadcaster who operates
solely over IP, meanwhile, might be reliant on manual subscriptions.

* [TVDNS](discovery/tvdns/) is a means of generating a fully-qualified domain name relating to a particular broadcast service. This domain name can then have service-discovery performed upon it.
* [DNS-SD](discovery/bonjour/) allows services to be advertised on local networks (using multicast DNS) and on provider networks by way of browse domains.
* [Domain Name Descriptor](discovery/dnd/) is a descriptor which can be inserted into a Service Descriptor Table (SDT) in order to advertise a DNS-style identifier for a broadcast service.
* [Manual subscription](discovery/subscriptions/) allows a user to subscribe to services not directly related to broadcast channels

#### 2. Provider-to-device (P2D) applications

One a device has obtained a domain name to perform discovery against, there
are a number of different kinds of application which can advertised by a
provider. These can be employed directly by a hybrid receiver device, but
can also be utilised by a “second screen”, or companion, device.

* A [service manifest](http://wiki.github.com/nexgenta/Baird/service-manifests) is an XML-based document which allows a broadcaster to describe the different channels it broadcasts and the different media which carries them. A device can perform matching and filtering and automatically augment a channel line-up.
* [Content feeds](http://wiki.github.com/nexgenta/Baird/content-feeds) are an Atom-based format which allows content, including audio, video and interactive sources, to be associated with individual programmes.
* The [Electronic Programme Guide](applications/epg/) application allows EPG data to be delivered out-of-band, typically over IP. This allows companion devices access to the same EPG information a traditional receiver already has, and also allows a hybrid receiver to augment its EPG data (for example, the additional data can contain information covering a longer period than the broadcast EPG, or might include images representing programmes).
* The [URI resolver](http://wiki.github.com/nexgenta/Baird/programmes-ontology-resolver) allows receiver or other device to obtain a canonical URL for a version of a programme. This URL can then be used as an identifier for other applications, or can be queried directly in order to receive programme information in various formats.

#### 3. Device-to-user (D2U) applications

As well as describing applications which can be advertised by service providers, there are a set of applications which can be advertised by devices, principally across local networks.

* The [pairing protocol](http://wiki.github.com/nexgenta/Baird/pairing-protocol) describes the mechanism for companion devices to be “paired” with receivers, resulting in a set of credentials which can be used with other applications.
* The [“Now Playing” (NOWP) protocol](specs/draft-mcroberts-nowp.html) allows a companion device to query a receiver for URIs identifying the current broadcast service (if any) and currently-playing item of content.
* The [remote control](http://wiki.github.com/nexgenta/Baird/ip-remote-control) protocol allows direct control of a receiver (or other playback device) over an IP network.
* [XMPP](http://wiki.github.com/nexgenta/Baird/xmpp) is a standard messaging protocol, commonly used for instant messaging (IM) applications, but increasingly for other kinds of inter-device and inter-user messaging.
* [Recommendations and sharing across the social graph](http://wiki.github.com/nexgenta/Baird/social-graph)

#### 4. Other supporting specifications

* [dvb: URI scheme](specs/draft-mcroberts-uri-dvb.html)
* [Device profiles](http://wiki.github.com/nexgenta/Baird/device-profiles)
* [Web application support for TV tuners](http://wiki.github.com/nexgenta/Baird/javascript-support-for-tuners)

### Key standards and related projects

There are a number of key standards and projects upon which Project Baird is
built.

* [Atom Syndication Format](http://atompub.org/)
* [HTML5](http://dev.w3.org/html5/spec/Overview.html)
* [NoTube](http://notube.org/)
* [P2P-Next](http://www.p2p-next.org/)
* [Programmes Ontology](http://www.bbc.co.uk/ontologies/programmes/) and [Music Ontology](http://musicontology.com/)
* [RadioDNS](http://www.radiodns.org/)
* [TV-Anytime](http://www.tv-anytime.org/)
* [Atlas](http://docs.atlasapi.org/)
* [XRD](http://hueniverse.com/xrd/)

(Please note that there is no formal affiliation between Project Baird
and the above; they are simply those projects which are particularly important
to this effort).
