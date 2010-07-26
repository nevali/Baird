---
title: Application Discovery
layout: default
section: specs
path: discovery/
---

<em>Service discovery</em> is the process by which IP-based applications, 
including audiovisual content feeds, can be located by connected and
companion devices.

There are serveral different sources of advertisements of applications:

* [Manual subscriptions](subscriptions/)
* [DNS-SD browse domains](bonjour/) (including <code>.local</code>, the mDNS browse domain)
* Per-service domain names embedded in broadcast Service Information tables
* [TVDNS domains](tvdns/)

Layered above the discovery layer are the IP-delivered [applications](/applications/) themselves.
