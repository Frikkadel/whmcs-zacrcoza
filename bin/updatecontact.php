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


if ($argc <= 3) {
    echo "Usage: updatecontact.php <handle>\n";
    echo "Please enter contact handle, field and value to update\n";
    echo "Fields:\n";
    echo "\temail\n\ttelephone\n\tname\n\torganization\n\taddress1\n\taddress2\n\tpostcode\n\tcity\n\tcountry\n";
    die();
}

list(, $handle, $field, $value) = $argv;

echo "handle: $handle\n";

echo "Updating contact\n";

$conn = new zacrBase($params);

try {
    $contactResponse = $conn->getContact($handle);
    $contact = $contactResponse->getContact();
    switch ($field) {
        case 'email' : $contactResponse->setEmail($value);
            break;
        case 'telephone' : $contactResponse->setVoice($value);
            break;
        case 'name' :
            $postalInfo = $contact->getPostalInfo(0);

            if ($postalInfo instanceof eppContactPostalInfo) {
                $postalInfo->setName($value);
            }
            $contact->setPostalInfo(0, $postalInfo);
            break;
        case 'organization' : $contactResponse->setVoice($value);
            break;
    }
    echo "ID: " . $contactResponse->getContactId() . "\n";
    echo "ROID: " . $contactResponse->getContactRoid() . "\n";
    echo "Client ID: " . $contactResponse->getContactClientId() . "\n";
    echo "Create Client ID: " . $contactResponse->getContactCreateClientId() . "\n";
    echo "Update Date: " . $contactResponse->getContactUpdateDate() . "\n";
    echo "Create Date: " . $contactResponse->getContactCreateDate() . "\n";
    echo "Status: " . $contact->getStatus() . "\n";
    echo "Voice #: " . $contact->getVoice() . "\n";
    echo "Fax #: " . $contact->getFax() . "\n";
    echo "Email: " . $contact->getEmail() . "\n";
    $postalInfo = $contact->getPostalInfo(0);
    echo "Name: " . $postalInfo->getName() . "\n";
    echo "Street: " . implode(',', $postalInfo->getStreets()) . "\n";
    echo "City: " . $postalInfo->getCity() . "\n";
    echo "Postal: " . $postalInfo->getZipcode() . "\n";
    echo "Province: " . $postalInfo->getProvince() . "\n";
    echo "Country: " . $postalInfo->getCountrycode() . "\n";
    echo "Company: " . $postalInfo->getOrganisationName() . "\n";
    echo "Postal Type: " . $postalInfo->getType() . "\n";

    $res = $conn->updateContact($handle, $contact);
} catch (eppException $e) {
    echo "Caught Exception - " . $e->getMessage() . "\n";
}
