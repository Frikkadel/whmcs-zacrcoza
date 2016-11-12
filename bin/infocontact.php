<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

define('ROOTDIR', realpath(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));

$include_path = dirname(dirname(__FILE__));
set_include_path($include_path . PATH_SEPARATOR . get_include_path());

// Base EPP objects
include_once('Protocols/EPP/eppConnection.php');
include_once('ZACR/cozaEppConnection.php');
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
    echo "Usage: infocontact.php <handle>\n";
    echo "Please enter contact handle view\n\n";
    die();
}

list(, $handle) = $argv;

echo "handle: $handle\n";

echo "Viewing contact\n";

$conn = new zacrBase($params);

try {
    $contact = $conn->getContact($handle);
    if ($contact) {
        echo "ID: " . $contact->getContactId() . "\n";
        echo "ROID: " . $contact->getContactRoid() . "\n";
        echo "Client ID: " . $contact->getContactClientId() . "\n";
        echo "Create Client ID: " . $contact->getContactCreateClientId() . "\n";
        echo "Update Date: " . $contact->getContactUpdateDate() . "\n";
        echo "Create Date: " . $contact->getContactCreateDate() . "\n";
        echo "Status: " . $contact->getContactStatusCSV() . "\n";
        echo "Voice #: " . $contact->getContactVoice() . "\n";
        echo "Fax #: " . $contact->getContactFax() . "\n";
        echo "Email: " . $contact->getContactEmail() . "\n";
        echo "Name: " . $contact->getContactName() . "\n";
        echo "Street: " . $contact->getContactStreet() . "\n";
        echo "City: " . $contact->getContactCity() . "\n";
        echo "Postal: " . $contact->getContactZipcode() . "\n";
        echo "Province: " . $contact->getContactProvince() . "\n";
        echo "Country: " . $contact->getContactCountrycode() . "\n";
        echo "Company: " . $contact->getContactCompanyname() . "\n";
        echo "Postal Type: " . $contact->getContactPostalType() . "\n";
    } else {
        var_dump($contact);
    }
} catch (eppException $e) {
    echo $e->getMessage() . "\n";
}
