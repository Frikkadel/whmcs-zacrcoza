<?php


class cozaCreateDomainRequest extends eppCreateDomainRequest
{

    /**
     *
     * @param eppDomain $domain
     * @return domElement
     */
    public function setDomain(eppDomain $domain)
    {
        parent::setDomain($domain);
        $list = $this->domainobject->getElementsByTagName('domain:name');
        for ($list as $node) {
            $node->appendChild( $node->createAttribute( 'xsi:schemaLocation', 'urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd' ));
        }
        return;
    }
}

