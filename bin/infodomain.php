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

include_once('bin/credentials.php');

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
    infodomain($conn, $conn->domainInfo($domainname));
} catch (eppException $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

function infodomain($conn, $response) {
    try {
        /* @var $response eppInfoDomainResponse */
        $d = $response->getDomain();
        echo "Info domain for " . $d->getDomainname() . ":\n";
        echo "Created on " . $response->getDomainCreateDate() . "\n";
        echo "Last update on " . $response->getDomainUpdateDate() . "\n";
        ShowContact($conn->getContact($d->getRegistrant()));
        echo "Registrant " . $d->getRegistrant() . "\n";
        echo "Contact info:\n";
        foreach ($d->getContacts() as $contact) {
            echo "  " . $contact->getContactType() . ": " . $contact->getContactHandle() . "\n";
            ShowContact($conn->getContact($contact->getContactHandle()));
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

function ShowtContact(eppInfoContactResponse $contact) {
    echo "  Name: {$contact->getContactName()}\n";
    echo "  Postal Info: {$contact->getContactPostalInfo()}\n";
    echo "  Postal Type: {$contact->getContactPostalType()}\n";
    echo "  Street: {$contact->getContactStreet()}\n";
    echo "  Province: {$contact->getContactProvince()}\n";
    echo "  Zip: {$contact->getContactZipcode()}\n";
    echo "  Country: {$contact->getContactCountrycode()}\n";
    echo "  Voice: {$contact->getContactVoice()}\n";
    echo "  Fax: {$contact->getContactFax()}\n";
    echo "  Email: {$contact->getContactEmail()}\n";
    echo "  Status: {$contact->getContactStatusCSV()}\n";
}
