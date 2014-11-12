<?php
include_once(dirname(dirname(__FILE__)).'/Protocols/EPP/eppConnection.php');

#
# Load the SIDN specific additions
#
/*
include_once(dirname(__FILE__).'/sidnEppCreateContactRequest.php');
include_once(dirname(__FILE__).'/sidnEppPollRequest.php');
include_once(dirname(__FILE__).'/sidnEppPollResponse.php');
include_once(dirname(__FILE__).'/sidnEppCheckResponse.php');
include_once(dirname(__FILE__).'/sidnEppInfoDomainResponse.php');
include_once(dirname(__FILE__).'/sidnEppRenewRequest.php');
*/
class cozaEppConnection extends eppConnection
{

    public function __construct( $params )
    {
        parent::__construct(false);
        parent::setHostname($params['Server']);
        parent::setPort($params['Port']);
        parent::setUsername($params['Username']);
        parent::setPassword($params['Password']);
        parent::setTimeout(5);
        parent::setLanguage('en');
        parent::setVersion('1.0');
		if ($params['SSL']) {
			parent::enableCertification($params['Certificate'], $params['Passphrase']);
		}
		parent::addService('urn:ietf:params:xml:ns:domain-1.0', 'domain');
		parent::addService('urn:ietf:params:xml:ns:contact-1.0', 'contact');
		
        #parent::addExtension('sidn-epp-ext','http://rxsd.domain-registry.nl/sidn-ext-epp-1.0');
        #parent::enableDnssec();
        #parent::addCommandResponse('sidnEppPollRequest', 'sidnEppPollResponse');
        #parent::addCommandResponse('sidnEppCreateContactRequest', 'eppCreateResponse');
        #parent::addCommandResponse('eppCheckRequest', 'sidnEppCheckResponse');
        #parent::addCommandResponse('eppInfoDomainRequest', 'sidnEppInfoDomainResponse');
        #parent::addCommandResponse('sidnEppRenewRequest', 'eppRenewResponse');
    }

}
