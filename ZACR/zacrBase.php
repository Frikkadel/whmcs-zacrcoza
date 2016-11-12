<?php

# Copyright (c) 2014, Frikkadel.co.za
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
# Official Website:
# https://github.com/Frikkadel/whmcs-zacrcoza

class zacrBase {

    // The connection handling class
    protected $eppConnection = null;

    public function __construct($params) {
        $this->eppConnection = new cozaEppConnection($params);
        $this->eppConnection->connect() &&
            $this->login();
    }

    public function __destruct() {
        $this->logout();
    }

    public function login() {
        $login = new eppLoginRequest();
        if ((($response = $this->eppConnection->writeandread($login)) instanceof eppLoginResponse) && ($response->Success())) {
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        $logout = new eppLogoutRequest();
        if ((($response = $this->eppConnection->writeandread($logout)) instanceof eppLoginResponse) && ($response->Success())) {
            return true;
        } else {
            return false;
        }
    }

    public function domainInfo($domain) {
        $epp = new eppDomain($domain);
        $info = new eppInfoDomainRequest($epp);
        if ((($response = $this->eppConnection->writeandread($info)) instanceof eppInfoDomainResponse) && ($response->Success())) {
            return $response;
        }
        return false;
    }

    public function createContact($email, $telephone, $name, $organization, $address1, $address2, $postcode, $city, $country) {
        $req = new eppCreateContactRequest(
            new eppContact(new eppContactPostalInfo($name, $city, $country, $organization, $address1, $address2, $postcode), $email, $telephone)
        );
        if ((($response = $this->eppConnection->writeandread($req)) instanceof eppCreateResponse) && ($response->Success())) {
            return $response;
        }
        return false;
    }

    public function getContact($handle) {
        $req = new eppInfoContactRequest(
            new eppContactHandle($handle)
        );
        if ((($response = $this->eppConnection->writeandread($req)) instanceof eppInfoContactResponse) && ($response->Success())) {
            return $response;
        }
        return false;
    }

    public function deleteContact($handle) {
        $req = new eppDeleteRequest(
            new eppContactHandle($handle)
        );
        if ((($response = $this->eppConnection->writeandread($req)) instanceof eppDeleteResponse) && ($response->Success())) {
            return $response;
        }
        return false;
    }

    public function setNameServers($domain, $params) {
        $response = $self->domainInfo($domain);
        if ($response && $response instanceof eppInfoDomainResponse) {
            $remove = $response->getDomain();
        }

        $add = new eppDomain($domain);
        if ($params["ns1"]) {
            $add->addHost(new eppHost($params["ns1"]));
        }
        if ($params["ns2"]) {
            $add->addHost(new eppHost($params["ns2"]));
        }
        if ($params["ns3"]) {
            $add->addHost(new eppHost($params["ns3"]));
        }
        if ($params["ns4"]) {
            $add->addHost(new eppHost($params["ns4"]));
        }
        if ($params["ns5"]) {
            $add->addHost(new eppHost($params["ns5"]));
        }

        $update = new eppUpdateDomainRequest($domain);
    }

}
