---
title: Automatic Service Discovery with TVDNS
layout: default
---

### Preamble

Please see the [TVDNS wiki page](http://github.com/nexgenta/Baird/wikis/tvdns) for notes,
examples and discussion relating to the TVDNS specification.

### Introduction

TVDNS is a mechanism for connected receivers to construct a fully-qualified
domain name (FQDN) for a particular broadcast service — typically, but not
necessarily, a television station.

TVDNS is an extension to [RadioDNS](http://radiodns.org/), and is defined along
the same lines: the TVDNS specification defines an additional root domain
(<code>tvdns.net</code>), and a mapping scheme which formulates a FQDN based
upon specific values from the DVB Service Information (SI).

Once a fully-qualified domain name has been constructed, a connected device
can perform DNS lookups in order to locate IP-delivered applications relating
to that station. These applications are principally advertised by way of <code>SRV</code>
records. Further background can be found on the [RadioDNS website](http://radiodns.org/about-radiodns/).

### Specification (r01 - 2010-06-12)

The TVDNS specification is defined as a set of amendments to the [RadioDNS Technical Specification (RDNS01 v0.6.1 — 2009-06-15)](http://radiodns.org/wp-content/uploads/2009/03/rdns011.pdf) (PDF).

#### Technical Specification → Concept

Replace:

<blockquote>In this document the domain “<code>radiodns.org</code>” is used solely to illustrate a unique namespace against which to prepend service parameters to create a resolvable FQDN.</blockquote>

with

<blockquote>In this document the domains “<code>radiodns.org</code>” and “<code>tvdns.net</code>” are used solely to illustrate unique namespaces against which to prepend service parameters to create resolvable FQDNs.</blockquote>

Replace diagram first box caption:

<blockquote>Construct “radiodns.org” URI and query DNS

DNS returns a CNAME</blockquote>

with:

<blockquote>Construct “radiodns.org” or “tvdns.net” fully-qualified domain name and query DNS

DNS may return a CNAME.</blockquote>

Replace diagram second box caption:

<blockquote>Query DNS using returned CNAME to locate SRV records</blockquote>

with:

<blockquote>If a CNAME was returned, query DNS using returned CNAME to locate SRV records</blockquote>

#### Technical Specification → Implementation → Digital Video Broadcasting (DVB)

Append new section:

<blockquote>The DVB family of broadcasting systems identify services through a combination of several 16-bit identifiers: a <code>network_id</code> and an <code>original_network_id</code>, which are assigned by and registered with [DVB Services](http://www.dvbservices.com/), and a <code>transport_stream_id</code> and <code>service_id</code>, both of which are assigned by the registrant of the <code>network_id</code> value. The combination of these three values identifies a particular service, although a given service might be assigned different combinations of identifiers in different broadcasting regions, and may be broadcast by multiple distinct platforms each with  their own <code>network_id</code> registration and internal identifier assignments.

The FQDN for a DVB service is compiled as follows:

<code>&lt;network_id&gt;.&lt;service_id&gt;.&lt;transport_stream_id&gt;.&lt;original_network_id&gt;.dvb.tvdns.net.</code>

The parameters are populated as follows:
<table><thead><tr><th scope="col">Parameters</th><th scope="col">Description</th><th scope="col">Value</th><th scope="col">Status</th></tr></thead>
<tbody><tr><th scope="row"><code>network_id</code></th><td><strong>Network Identifier</strong>
The network identifier assigned to the network by DVB Services, or an internally-assigned network identifier taken from the “reusable” network identifier blocks</td><td>4-digit hexadecimal</td><td>mandatory</td></tr>
<tr><th scope="row"><code>service_id</code></th><td><strong>Service Identifier</strong>
The service identifier assigned by the platform to a particular station.</td><td>4-digit hexadecimal</td><td>mandatory</td></tr>
<tr><th scope="row"><code>transport_stream_id</code></th><td><strong>Transport Stream (TS) Identifier</strong>
The transport stream identifier assigned by the platform to a particular station.</td><td>4-digit hexadecimal</td><td>mandatory</td></tr>
<tr><th scope="row"><code>original_network_id</code></th><td><strong>Original Network Identifier (ONID)</strong>
The ONID assigned to the platform by DVB Services</td><td>4-digit hexadecimal</td><td>mandatory</td></tr>
</tbody>
</table>

The device should acquire the values for <code>original_network_id</code>, <code>network_id</code>, <code>transport_stream_id</code> and <code>service_id</code> from the DVB/MPEG Transport Stream.

The <code>original_network_id</code>, <code>network_id</code>, <code>transport_stream_id</code> and <code>service_id</code> values are contained within the Network Information Table (NIT) defined by ETSI EN 300 468 “Digital Video Broadcasting (DVB); Specification for Service Information (SI) in DVB systems”.
</blockquote>

#### Technical Specification → Implementation → FQDN construction for IP-delivered services

Replace:

<blockquote>A CNAME record unique to each radio service provider must be defned and used to deliver streaming content so that SRV records can be held against this domain instead.</blockquote>

with:

<blockquote>In order to perform application discovery, a client will query DNS for any SRV records associated with the FQDN <code>s1.stream.provider.net.</code>. This FQDN may be a CNAME (unique to each service) to ease domain management, with SRV records associated with the domain name that is the target of the CNAME.</blockquote>

Replace:

<blockquote>Devices must lookup SRV records on the inital FQDN that the stream is located and not the resultng domain afer CNAME resoluton.</blockquote>

<blockquote>Devices must perform DNS queries for SRV records on the initial FQDN specified in the stream URL, even if that FQDN is known to be a CNAME, or where initiating streaming from that URL results in a response temporarily redirecting the client to an alternative URL.</blockquote>

#### Technical Specification → Implementation → Application discovery

Replace:

<blockquote>Once the relevant CNAME record has been derived for a service based on the details above, it is antcipated that broadcasters will advertse available applicatons through the use of SRV records associated with that domain record.</blockquote>

with:

<blockquote>Once the relevant fully-qualified domain name has been determined for a service based upon the details above, it is anticipated that broadcasters will advertise services through the use of SRV records associated with that domain. Broadcasters may make use of CNAME records or wildcards in order to simplify domain management processes. The implementation of application discovery should follow normal DNS resolution procedures, including the following of CNAMEs, as specified in RFC1034 (“Domain Names — Concepts and Facilities”).</blockquote>

In the list headed “For IP delivered services:”, replace:

<blockquote>4. Client connects and streams from resolved address <code>http://stream.musicradio.com/stream.mp3</code></blockquote>

with:

<blockquote><p>4. Client resolves <code>stream.musicradio.com</code> to a host address.</p>
<p>5. Client establishes an HTTP connection to the host and begins streaming <code>/stream.mp3</code>, specifying a <code>Host:</code> request header of <code>fabfm.musicradio.com</code>.</p></blockquote>

