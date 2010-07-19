---
title: Project Baird namespace
layout: default
---

<code>http://projectbaird.com/ns/</code> is the namespace used for Project
Baird-specific XML elements, attributes and link relations.

XML attributes:

* <code>dtstart</code>: XML attribute; when attached to an <code>atom:link</code>, indicates the start of an availability window (ISO 8601 format datetime)
* <code>dtend</code>: XML attribute; when attached to an <code>atom:link</code>, indicates the end of an availability window (ISO 8601 format datetime)

XRD properties:

* <code>serviceClass</code>: Specifies the class of service provided by the linked resource (applicable values are <code>demand</code>, <code>linear</code>, and <code>interactive</code>)
* <code>delivery</code>: Specifies the delivery mechanism of a linked resource (applicable values are <code>unicast</code>, <code>unicast-v4</code>, <code>unicast-v6</code>, <code>multicast</code>, <code>multicast-v4</code>, and <code>multicast-v6</code>)
* <code>media</code>: Specifies the media query which must be satisfied to present the linked resource; aligned with [CSS3 Media Queries](http://dev.w3.org/csswg/css3-mediaqueries/)
* <code>serviceNumberPreference</code>: Specifies the numeric channel number which should be allocated to the service if possible. Multiple values may be given, separated by breaking whitespace characters Values may take the form of a channel number, or may be in the form <code><em>number</em>=<em>platform</em></code>, where <code>platform</code> is the URI of a service which the device supports (described separately; intent is to allow different channel numbering schemes between platforms; e.g., BBC 1 on Sky Digital and Virgin Media is 101, on Freeview is 1, on UPC Netherlands is 19, etc.)
* <code>width</code>: Specifies the width, in pixels, of the linked resource (principally for foaf:Description, but also for po:Channel subclasses)
* <code>height</code>: Specifies the height, in pixels, of the linked resource (principally for foaf:Description, but also for po:Channel subclasses)
* <code>preferredBackground</code>: Specifies the preferred matte background colour which should be used for the container of the linked resource, if possible (for foaf:Depiction); format is <code>#rrggbb</code> or <code>#rgb</code>; symbolic colour names are not supported.
