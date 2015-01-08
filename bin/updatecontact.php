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


if ($argc <= 2)
{
    echo "Usage: updatecontact.php <contactid> <what> <value>\n";
	echo "Please enter contact details to modify\n\n";
	die();
}

list(,$contact,$what,$value)= $argv;

$params = getregistrarconfigoptions('ZACRcoza');
$conn = new zacrBase($params);
echo "Updating $contact's $what\n";
try {
	//$conn->updateContact( $contact, $email,$telephone,$name,$organization,$address1,$address2,$postcode,$city, $country );
	switch ($what) {
		case 'email' :
			$result = $conn->updateContact( $contact, $value,null,null,null,null,null,null,null,null );
			break;
		case 'telephone' :
			$result = $conn->updateContact( $contact, null,$value,null,null,null,null,null,null,null );
			break;
		case 'telephone' :
			$result = $conn->updateContact( $contact, null,$value,null,null,null,null,null,null,null );
			break;
		case 'name' : 
			$result = $conn->updateContact( $contact, null,null,$value,null,null,null,null,null,null );
			break;
		case 'organization' :
			$result = $conn->updateContact( $contact, null,null,null,$value,null,null,null,null,null );
			break;
		case 'address1' :
			$result = $conn->updateContact( $contact, null,null,null,null,$value,null,null,null,null );
			break;
		case 'address2' :
			$result = $conn->updateContact( $contact, null,null,null,null,null,$value,null,null,null );
			break;
		case 'postcode' :
			$result = $conn->updateContact( $contact, null,null,null,null,null,null,$value,null,null );
			break;
		case 'city' :
			$result = $conn->updateContact( $contact, null,null,null,null,null,null,null,$value,null );
			break;
		case 'country' :
			$result = $conn->updateContact( $contact, null,null,null,null,null,null,null,null,$value );
			break;
		default	: echo "<what> must be either email, telephone, name, organization, address1, address2, postcode, city or country </what>\n";
	}

	print_r($result);
}
catch (eppException $e)
{
	echo $e->getMessage()."\n";
}
