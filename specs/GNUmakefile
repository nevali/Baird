## Override these in the environment or on the command-line
## if they don't suit.

XML2RFC ?= xml2rfc
TCLSH ?= tclsh

## Targets

OUT = \
	draft-mcroberts-uri-dvb.txt draft-mcroberts-uri-dvb.html \
	draft-mcroberts-uri-resolver.txt draft-mcroberts-uri-resolver.html \
	draft-mcroberts-baird-overview.txt draft-mcroberts-baird-overview.html \
	draft-mcroberts-nowp.txt draft-mcroberts-nowp.html \
	draft-mcroberts-remote-pairing.txt draft-mcroberts-remote-pairing.html


## Rules

all: $(OUT)

clean:
	rm -f $(OUT)

%.txt: %.xml
	$(XML2RFC) $< $@

%.html: %.xml
	$(XML2RFC) $< $@
