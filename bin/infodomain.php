<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

define('ROOTDIR', realpath(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));

$include_path = dirname(dirname(__FILE__));
set_include_path($include_path . PATH_SEPARATOR . get_include_path());

// Base EPP objects
include_once('Protocols/EPP/eppConnection.php');
include_once('Protocols/EPP/eppRequests/eppLoginRequest.php');
include_once('Protocols/EPP/eppResponses/eppLoginResponse.php');
include_once('ZACR/cozaEppConnection.php');

// Base EPP commands: hello, login and logout
include_once('ZACR/zacrBase.php');

# Include registrar functions aswell
#require_once ROOTDIR . '/init.php';
#require_once ROOTDIR . '/includes/functions.php';
#require_once ROOTDIR . '/includes/registrarfunctions.php';
# Grab module parameters
#$params = getregistrarconfigoptions('ZACRcoza');
$params = array(
    'Server' => 'coza-otande.registry.net.za',
    'Port' => '3121',
    'Username' => 'frikkadel',
    'Password' => 'd78884ad69',
    'SSL' => true,
    'Certificate' => '/home/willo/development/frikkadel/frikkadel/modules/registrars/frikkadel-zacr/cert/epp-2013-03.pem',
    'Passphrase' => ''
);

/*
 * This script checks for the availability of domain names
 *
 * You can specify multiple domain names to be checked
 */


if ($argc <= 1) {
    echo "Usage: infodomain.php <domainname>\n";
    echo "Please enter a domain name retrieve\n\n";
    die();
}

$domainname = $argv[1];

echo "Retrieving info on " . $domainname . "\n";

$conn = new zacrBase($params);

try {
    infodomain($conn->domainInfo($domainname));
} catch (eppException $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

function infodomain($response) {
    try {
        /* @var $response eppInfoDomainResponse */
        $d = $response->getDomain();
        echo "Info domain for " . $d->getDomainname() . ":\n";
        echo "Created on " . $response->getDomainCreateDate() . "\n";
        echo "Last update on " . $response->getDomainUpdateDate() . "\n";
        echo "Registrant " . $d->getRegistrant() . "\n";
        echo "Contact info:\n";
        foreach ($d->getContacts() as $contact) {
            echo "  " . $contact->getContactType() . ": " . $contact->getContactHandle() . "\n";
        }
        echo "Nameserver info:\n";
        foreach ($d->getHosts() as $nameserver) {
            echo "  " . $nameserver->getHostName() . "\n";
        }
    } catch (eppException $e) {
        echo 'ERROR1';
        echo $e->getMessage() . "\n";
    }
}
