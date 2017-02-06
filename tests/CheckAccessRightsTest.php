<?php
namespace Odoo\Client\Test;

use PhpXmlRpc\Value as xmlrpcval;
use Odoo\Client\OdooClient;

class CheckAccessRightsTest extends TestOdooClient
{
    public function testOne()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $rights = array(
            new xmlrpcval('read', xmlrpcval::$xmlrpcString)
        );
        $boolean = $client->check_access_rights($this->_model_name, $rights);

        $this->assertTrue(is_bool($boolean));
        $this->assertTrue($boolean);
    }

    public function testTwo()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $rights = array(
            new xmlrpcval('read', xmlrpcval::$xmlrpcString),
            new xmlrpcval('create', xmlrpcval::$xmlrpcString)
        );
        $boolean = $client->check_access_rights($this->_model_name, $rights);

        $this->assertTrue(is_bool($boolean));
        $this->assertTrue($boolean);
    }
}
