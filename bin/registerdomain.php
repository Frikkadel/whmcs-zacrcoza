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
$conn = new zacrBase($params);

if (!checkhosts($conn, array('daredevil.wcic.co.za')))
{
	//createhost($conn,'ns1.metaregistrar.nl');
}

#if (!checkhosts($conn, array('ns2.metaregistrar.nl')))
#{
#	createhost($conn,'ns2.metaregistrar.nl');
#}

function checkhosts($conn, $hosts)
{
	try
	{
		if ($response = $conn->checkHosts($hosts))
		{
			$checks = $response->getCheckedHosts();
            $allchecksok = true;
			foreach ($checks as $hostname => $check)
			{
				echo "$hostname ".($check ? 'does not exist' : 'exists')."\n";
                if ($check)
                {
                    $allchecksok = false;
                }
			}
            return $allchecksok;
		}
	}
	catch (eppException $e)
	{
		echo $e->getMessage()."\n";
	}
}