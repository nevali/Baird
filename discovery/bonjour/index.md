---
title: Service discovery on a local network
layout: default
path: discovery/bonjour/
---

For some [services](/services/), it is more appropriate that they be
advertised and discovered automatically by devices on a local wired or
wireless network, rather than advertised by a broadcaster and discovered
by a connected receiver.

For these purposes, Baird specifies the use of [Bonjour](http://www.apple.com/support/bonjour/) — that is,
[DNS-SD](http://www.dns-sd.org/) in concert with [Multicast DNS](http://www.multicastdns.org/). Bonjour is
widely-deployed in a range of different devices, with a number of competing implementations in existence.
