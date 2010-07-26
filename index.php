<?php

require(dirname(__FILE__) . '/templates/toys/header.phtml');

?>
			<div class="section">
				<dl>
					<dt><a href="/lookup/">Lookup Tool</a></dt>
					<dd>
						<p>
							Perform service-discovery against RadioDNS/TVDNS domains. Also allows
							limited testing of URI resolver service. Demonstrates RadioDNS/TVDNS,
							URI resolver.
						</p>
						<ul>
							<li><a href="/lookup/?kind=dvb&original_network_id=233a&transport_stream_id=1041&service_id=10bf&network_id=3098">BBC One (Bristol)</a></li>
							<li><a href="/lookup/?kind=fm&country=ce1&pi=c586&freq=09580">Capital FM (London)</a></li>
						</ul>
					</dd>
					<dt><a href="/boxify/?kind=dvb&onid=233a&nid=3098">Boxify</a></dt>
					<dd>
						<p>
							Simulates the service discovery and matching processes a receiver or
							second-screen device would undertake. Demonstrates TVDNS, XRD processing,
							service matching.
						</p>
						<ul>
							<li><a href="/boxify/?kind=dvb&onid=233a&nid=3098">Standard channel line-up</a></li>
							<li><a href="/boxify/?kind=dvb&onid=233a&nid=3098&xrd=1">Standard channel line-up with XRD processing enabled</a></li>
							<li><a href="/boxify/?kind=dvb&onid=233a&nid=3098&xrd=1&xrdurl[]=seesaw-sample">Standard channel line-up with XRD processing enabled and a user service subscription</a></li>
						</ul>
					</dd>
					<dt><a href="/tablet/?kind=dvb&onid=233a&nid=3098&tsid=1041&sid=10bf">Tablet</a></dt>
					<dd>
						<p>
							Simple (and currently limited) iPad UI mock-up for a “second screen” application. Demonstrates
							TVDNS, XRD processing, service matching, URI resolution service, Programmes Ontology, all working
							in tandem.
						</p>
						<ul>
							<li><a href="/tablet/?kind=dvb&onid=233a&nid=3098&tsid=1041&sid=1041">BBC One (West)</a></li>
							<li><a href="/tablet/?kind=dvb&onid=233a&nid=3098&tsid=1041&sid=10bf">BBC Two (England)</a></li>
						</ul>
					</dd>
				</dl>
			</div>
<?php
require_once(dirname(__FILE__) . '/templates/toys/footer.phtml');
