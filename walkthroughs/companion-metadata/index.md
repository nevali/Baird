---
title: Retrieving broadcast-related metadata on companion devices
layout: default
---

1. Locate the [remote control application](http://github.com/nexgenta/Baird/wiki/IP-remote-control)
using Bonjour.

2. If required, use the [pairing protocol](http://github.com/nexgenta/Baird/wiki/Pairing-protocol)
to pair with the receiver.

3. Request details of the currently-airing programme via the remote control
application; this returns <em>basic</em> information relating to the current
service and broadcast, including the TVDNS or RadioDNS domain name for the
service, the <code>crid://</code> URI for the broadcast, and the scheduled
start time, where known.

4. Companion device queries for metadata exactly as [the receiver would](../broadcast-metadata/).
