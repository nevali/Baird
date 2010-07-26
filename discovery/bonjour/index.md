---
title: Service discovery on a private network
layout: default
section: specs
path: discovery/bonjour/
---

For some [applications](/applications/), it is more appropriate that they be
advertised and discovered automatically by devices on a local wired or
wireless network, rather than advertised by a broadcaster and discovered
by a connected receiver.

For these purposes, Baird specifies the use of [DNS-SD](http://www.dns-sd.org/)
in concert with [Multicast DNS](http://www.multicastdns.org/), that is, the
Service Discovery aspect of [Bonjour](http://developer.apple.com/mac/library/documentation/Cocoa/Conceptual/NetServices/Articles/about.html#//apple_ref/doc/uid/TP40002458-TPXREF109).

Bonjour is widely-deployed in a range of different devices, with a number of
competing implementations in existence, from both [Apple](http://opensource.apple.com/source/mDNSResponder/)
and [others](http://avahi.org/).

Beyond device-to-user (D2U) applications, Bonjour-based service discovery
can be employed to allow applications provisioned on a local network, or
on a service-provider’s network, to be advertised alongside those discoverable
by other means.

Devices should support both local (multicast-based) DNS service discovery
as well wide-area discovery.
