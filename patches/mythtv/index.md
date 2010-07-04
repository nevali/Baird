--
title: Patches: MythTV
layout: default
path: patches/mythtv/
--

This patch populates a 'programuri' column to the 'programs' table of MythTV's
database with a transport-specific URI for the program. At present, only
dvb: URIs are supported, but other transports could do something similar.

Because modifying the MythTV database schema through normal means would
involve a change to the schema version number, and this would break
compatibility with future upstream updates to MythTV, altering the database
schema must be performed manually.

In order to do this, execute the following SQL:

<p><code>ALTER TABLE `programs` ADD `programuri` TINYTEXT DEFAULT NULL;</code></p>

[Download the patch](00-programuri.diff).

To apply the patch, change to the top-level <code>mythtv</code> directory
(which contains <code>themes</code>, <code>mythtv</code>, etc.), and
run:

<p><code>patch -p0 &lt; /path/to/00-programuri.diff</code></p>

If you’re already inside the <code>mythtv/mythtv</code> directory (that is, you have
a <code>libs</code> subdirectory in the current directory), you can strip off
the first path component of the patch when applying:

<p><code>patch -p1 &lt; /path/to/00-programuri.diff</code></p>
