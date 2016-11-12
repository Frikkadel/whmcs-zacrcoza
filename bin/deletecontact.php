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
    echo "Usage: deletecontact.php <handle>\n";
    echo "Please enter contact handle to delete\n\n";
    die();
}

list(, $handle) = $argv;

echo "handle: $handle\n";

echo "Deleting contact\n";

$conn = new zacrBase($params);

try {
    $response = $conn->deleteContact($handle);
    if ($response) {
        echo "Contact ID: " . $handle . " deleted\n";
    } else {
        var_dump($response);
    }
} catch (eppException $e) {
    echo $e->getMessage() . "\n";
}
