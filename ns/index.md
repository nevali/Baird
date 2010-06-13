---
title: Project Baird namespace
layout: default
---

<code>http://projectbaird.com/ns/</code> is the namespace used for Project
Baird-specific XML elements, attributes and link relations.

The following identifiers are currently defined:

* <code>dtstart</code>: XML attribute; when attached to an <code>atom:link</code>, indicates the start of an availability window (ISO 8601 format datetime)
* <code>dtend</code>: XML attribute; when attached to an <code>atom:link</code>, indicates the end of an availability window (ISO 8601 format datetime)
* <code>demand</code>: Link relation extension; indicates that the linked resource provides on-demand download or streaming of the described content
* <code>linear</code>: Link relation extension; indicates that the linked resource provides linear streaming of the described content
