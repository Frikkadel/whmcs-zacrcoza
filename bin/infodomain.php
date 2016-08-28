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

// Base EPP commands: hello, login and logout
include_once('ZACR/zacrBase.php');

# Include registrar functions aswell
require_once ROOTDIR . '/init.php';
require_once ROOTDIR . '/includes/functions.php';
require_once ROOTDIR . '/includes/registrarfunctions.php';


# Grab module parameters
$params = getregistrarconfigoptions('ZACRcoza');

/*
 * This script checks for the availability of domain names
 *
 * You can specify multiple domain names to be checked
 */


if ($argc <= 1)
{
    echo "Usage: infodomain.php <domainname>\n";
	echo "Please enter a domain name retrieve\n\n";
	die();
}

$domainname = $argv[1];

echo "Retrieving info on ".$domainname."\n";
try
{
    $conn = new cozaEppConnection($params);

    // Connect to the EPP server
    if ($conn->connect())
    {
        if (login($conn))
        {
            infodomain($conn, $domainname);
            logout($conn);
        }
    }
    else
    {
        echo "ERROR CONNECTING\n";
    }
}
catch (eppException $e)
{
    echo "ERROR: ".$e->getMessage()."\n\n";
}



function infodomain($conn, $domainname)
{
	try
	{
        $epp = new eppDomain($domainname);
		$info = new eppInfoDomainRequest($epp);
		if ((($response = $conn->writeandread($info)) instanceof eppInfoDomainResponse) && ($response->Success()))
		{
            /* @var $response eppInfoDomainResponse */
            $d = $response->getDomain();
            echo "Info domain for ".$d->getDomainname().":\n";
            echo "Created on ".$response->getDomainCreateDate()."\n";
            echo "Last update on ".$response->getDomainUpdateDate()."\n";
            echo "Registrant ".$d->getRegistrant()."\n";
            echo "Contact info:\n";
            foreach ($d->getContacts() as $contact)
            {
                echo "  ".$contact->getContactType().": ".$contact->getContactHandle()."\n";
            }
            echo "Nameserver info:\n";
            foreach ($d->getHosts() as $nameserver)
            {
                echo "  ".$nameserver->getHostName()."\n";
            }

		}
        else
        {
            echo "ERROR2\n";
        }
	}
	catch (eppException $e)
	{
        echo 'ERROR1';
		echo $e->getMessage()."\n";
	}
}
