<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

define( 'ROOTDIR', realpath( dirname(dirname(dirname(dirname(dirname(__FILE__)))))));

$include_path = dirname(dirname(__FILE__));
set_include_path($include_path . PATH_SEPARATOR . get_include_path());

// Base EPP objects
include_once('Protocols/EPP/eppConnection.php');
include_once('ZACR/cozaEppConnection.php');
include_once('ZACR/zacrBase.php');

# Include registrar functions aswell
require_once ROOTDIR . '/dbconnect.php';
require_once ROOTDIR . '/includes/functions.php';
require_once ROOTDIR . '/includes/registrarfunctions.php';


# Grab module parameters
$params = getregistrarconfigoptions('ZACRcoza');

/*
 * This script checks for the availability of domain names
 *
 * You can specify multiple domain names to be checked
 */


if ($argc <= 8)
{
    echo "Usage: createcontact.php <email> <telephone> <name> <organization> <address1> <address2> <postcode> <city> <country>\n";
	echo "Please enter contact details to create\n\n";
	die();
}

list(,$email,$telephone,$name,$organization,$address1,$address2,$postcode,$city, $country)= $argv;

echo "email: $email\ntelephone: $telephone\nname: $name\norganization: $organization\naddress1: $address1\naddress2: $address2\npostcode: $postcode\ncity: $city\ncountry: $country\n";

echo "Creating contact\n";

$params = getregistrarconfigoptions('ZACRcoza');
$conn = new zacrBase($params);

try {
	$result = $conn->createContact( $email,$telephone,$name,$organization,$address1,$address2,$postcode,$city, $country );
	print_r($result);
}
catch (eppException $e)
{
	echo $e->getMessage()."\n";
}
