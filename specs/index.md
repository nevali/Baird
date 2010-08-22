---
title: Specifications
layout: default
section: specs
path: specs/
---

The Project Baird Specifications will be published here as they are written.

You can track project activity by watching the [project on GitHub](http://github.com/nexgenta/Baird).

Early drafts and notes can be found on the [GitHub Wiki](http://github.com/nexgenta/Baird/wiki/).

### Part 1: Introduction and overview

1. Introduction and project overview ([work-in-progress draft](draft-mcroberts-baird-overview.html))

### Part 2: Application discovery

1. Generating fully-qualified domain names for broadcast services with TVDNS ([given as amendments to RadioDNS specification](/discovery/tvdns/))
2. Service discovery on private networks with DNS-SD and Multicast DNS ([current explanatory note](/discovery/bonjour/))
3. Embedding a fully-qualified domain name identifying a broadcast service in a Service Descriptor Table (SDT)
4. Enabling user-initiated and pre-configured manual subscriptions ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/Manual-service-subscription))

### Part 3: Provider-to-device (P2D) applications

1. Augmenting service information with XRD-based service manifests ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/Service-manifests), [sample XRD](http://github.com/nexgenta/Baird/blob/gh-pages/applications/manifests/sample-bbc.xml))
2. Augmenting programme information with Atom-based service feeds ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/Service-feeds))
3. Extending RadioEPG to support television services ([introductory note](http://baird.nx/applications/epg/))
4. Providing visualisations for audio-only services ([RadioDNS/RadioVIS specification document](http://radiodns.org/wp-content/uploads/2009/12/RVIS01_1.0.0.pdf))
5. Capturing audience interest ([RadioDNS/RadioTAG working draft document](http://radiodns.org/wp-content/uploads/2009/03/rtag011.pdf))
6. Resolving event (programme) information to persistent URLs ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/Metadata-resolver))
7. XRD document discovery using DNS-SD ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/XRD-document-discovery-using-DNS-SD))

### Part 4: Device-to-user (D2U) applications

1. Protocol for establishing pairing relationships between devices ([work-in-progress draft](draft-mcroberts-remote-pairing.html))
2. Protocol for obtaining resource identifiers for services and events ([work-in-progress draft](draft-mcroberts-nowp.html))
3. Remote control protocol ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/IP-remote-control))
4. Provision for XMPP-based applications ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/XMPP))
5. Delivering recommendations for programmes (both automatic, and based upon social graph)

### Part 5: Conformance profiles

1. Content and device profiles ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/Profiles))

### Appendix A: Supporting specifications

1. [uri: DVB scheme Internet-Draft](draft-mcroberts-uri-dvb.html)
2. Supporting web applications on hybrid devices ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/Javascript-support-for-tuners))
3. Advertising device-provided service availability from behind NAT ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/SRV-registration-proxy)
4. XRD document extensions for web applications ([wiki-based notes](http://github.com/nexgenta/Baird/wiki/XRD-document-extensions-for-web-applications))

### Appendix B: Identifier registry

1. Registry of link relations, XML elements, XML attributes and XRD properties ([current in-progress registry](/ns/))
