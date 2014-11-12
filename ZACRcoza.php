<?php
# Copyright (c) 2014, Frikkadel.co.za
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

# Official Website:
# https://github.com/Frikkadel/whmcs-zacrcoza

// Make sure we not being accssed directly
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

function ZACRcoza_getConfigArray() {
	return array(
		"Username" => array( "FriendlyName"=> "Username", "Type" => "text", "Size" => "20", "Description" => "Enter your username here" ),
		"Password" => array( "FriendlyName"=> "Password", "Type" => "password", "Size" => "20", "Description" => "Enter your password here" ),
		"Server" => array( "FriendlyName"=> "Server", "Type" => "text", "Size" => "20", "Description" => "Enter EPP Server Address" ),
		"Port" => array( "FriendlyName"=> "Port", "Type" => "text", "Size" => "20", "Description" => "Enter EPP Server Port" ),
		"SSL" => array( "FriendlyName"=> "SSL", "Type" => "yesno" ),
		"Certificate" => array( "FriendlyName"=> "Certificate", "Type" => "text", "Description" => "Path of certificate .pem" ),
		"Passphrase" => array( "FriendlyName"=> "Passphrase", "Type" => "text", "Description" => "Pass phrase of certificate .pem" ),
	);
}

function ZACRcoza_AdminCustomButtonArray() {
	return array(
		"Approve Transfer" => "ApproveTransfer",
		"Cancel Transfer Request" => "CancelTransferRequest",
		"Reject Transfer" => "RejectTransfer",
		"Recreate Contact" => "RecreateContact",
	);
}

function ZACRcoza_GetNameservers($params) {
	# Grab variables
	$sld = $params["sld"];
	$tld = $params["tld"];
	$domain = strtolower("$sld.$tld");

	$values = array();
	# Get client instance
	try {
		$conn = ZACRcoza_factory();
		$response = $conn->domainInfo($domain);
		if ($response && $response instanceof eppInfoDomainResponse) {
			$d = $response->getDomain();
			$i=1;
			foreach ($d->getHosts() as $nameserver) {
				$values["ns".($i++)] = $nameserver->getHostName();
			}
			$values["status"] = $response->getResultMessage();
		} else {
			$values["error"] = 'ZACRcoza_GetNameservers: Error communicating to EPP Server';
		}
	} catch (Exception $e) {
		$values["error"] = 'ZACRcoza_GetNameservers: '.$e->getMessage();
	}
	return $values;
}

function ZACRcoza_SaveNameservers($params) {
	# Grab variables
	$sld = $params["sld"];
	$tld = $params["tld"];
	$domain = strtolower("$sld.$tld");
	
	$values = array();
	# Get client instance
	try {
		$conn = ZACRcoza_factory();
		$response = $conn->domainInfo($domain);
		if ($response && $response instanceof eppInfoDomainResponse) {
			$remove = $response->getDomain();
		}

	# Generate XML for nameservers
	if ($nameserver1 = $params["ns1"]) {
		$add_hosts = '
<domain:hostAttr>
	<domain:hostName>'.$nameserver1.'</domain:hostName>
</domain:hostAttr>
';
	}
	if ($nameserver2 = $params["ns2"]) {
		$add_hosts .= '
<domain:hostAttr>
	<domain:hostName>'.$nameserver2.'</domain:hostName>
</domain:hostAttr>
';
	}
	if ($nameserver3 = $params["ns3"]) {
		$add_hosts .= '
<domain:hostAttr>
	<domain:hostName>'.$nameserver3.'</domain:hostName>
</domain:hostAttr>
';
	}
	if ($nameserver4 = $params["ns4"]) {
		$add_hosts .= '
<domain:hostAttr>
	<domain:hostName>'.$nameserver4.'</domain:hostName>
</domain:hostAttr>';
	}
	if ($nameserver5 = $params["ns5"]) {
		$add_hosts .= '
<domain:hostAttr>
	<domain:hostName>'.$nameserver5.'</domain:hostName>
</domain:hostAttr>';
	}

	# Get client instance
	try {
		$client = _cozaepp_Client($tld);

		# Grab list of current nameservers
		$request = $client->request( $xml = '
<epp:epp xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:epp="urn:ietf:params:xml:ns:epp-1.0"
		xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
	<epp:command>
		<epp:info>
			<domain:info xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
				<domain:name hosts="all">'.$domain.'</domain:name>
			</domain:info>
		</epp:info>
	</epp:command>
</epp:epp>
');
		# Parse XML result
		$doc= new DOMDocument();
		$doc->loadXML($request);
		logModuleCall('Cozaepp', 'SaveNameservers', $xml, $request);

		# Pull off status
		$coderes = $doc->getElementsByTagName('result')->item(0)->getAttribute('code');
		$msg = $doc->getElementsByTagName('msg')->item(0)->nodeValue;
		# Check if result is ok
		if($coderes != '1000') {
			$values["error"] = "SaveNameservers/domain-info($domain): Code ($coderes) $msg";
			return $values;
		}

		$values["status"] = $msg;

		# Generate list of nameservers to remove
		$hostlist = $doc->getElementsByTagName('hostName');
		foreach ($hostlist as $host) {
			$rem_hosts .= '
<domain:hostAttr>
	<domain:hostName>'.$host->nodeValue.'</domain:hostName>
</domain:hostAttr>
	';
		}

		# Build request
		$request = $client->request($xml = '
<epp:epp xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:epp="urn:ietf:params:xml:ns:epp-1.0"
		xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xmlns:cozadomain="http://co.za/epp/extensions/cozadomain-1-0"
		xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
	<epp:command>
		<epp:update>
			<domain:update>
				<domain:name>'.$domain.'</domain:name>
				<domain:add>
					<domain:ns>'.$add_hosts.' </domain:ns>
				</domain:add>
				<domain:rem>
					<domain:ns>'.$rem_hosts.'</domain:ns>
				</domain:rem>
			</domain:update>
		</epp:update>
		<epp:extension>
			<cozadomain:update xsi:schemaLocation="http://co.za/epp/extensions/cozadomain-1-0 coza-domain-1.0.xsd">
			<cozadomain:chg><cozadomain:autorenew>false</cozadomain:autorenew></cozadomain:chg></cozadomain:update>
		</epp:extension>
	</epp:command>
</epp:epp>
	');

		# Parse XML result
		$doc= new DOMDocument();
		$doc->loadXML($request);
		logModuleCall('Cozaepp', 'SaveNameservers', $xml, $request);

		# Pull off status
		$coderes = $doc->getElementsByTagName('result')->item(0)->getAttribute('code');
		$msg = $doc->getElementsByTagName('msg')->item(0)->nodeValue;
		# Check if result is ok
		if($coderes != '1001') {
			$values["error"] = "SaveNameservers/domain-update($domain): Code ($coderes) $msg";
			return $values;
		}

		$values['status'] = "Domain update Pending. Based on .co.za policy, the estimated time taken is around 5 days.";

	} catch (Exception $e) {
		$values["error"] = 'SaveNameservers/EPP: '.$e->getMessage();
		return $values;
	}

	return $values;
}
/***********************************************************************
 * Private Utility function
 * ********************************************************************/ 
function ZACRcoza_factory() {
	# Setup include dir
	$include_path = dirname(dirname(__FILE__));
	set_include_path($include_path . PATH_SEPARATOR . get_include_path());

	# Include EPP stuff we need
	include 'Protocols/EPP/eppConnection.php';
	include 'ZACR/zacrBase.php';
	
	$params = getregistrarconfigoptions('ZACRcoza');
	return new zacrBase($params);
}
