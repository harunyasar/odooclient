<?php
namespace Odoo\Client\Test;

use PhpXmlRpc\Value as xmlrpcval;
use Odoo\Client\OdooClient;

class CheckAccessRightsTest extends TestOdooClient
{
    public function testRead()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $right = new xmlrpcval('read', xmlrpcval::$xmlrpcString);
        $boolean = $client->check_access_rights($this->_model_name, $right);

        $this->assertTrue(is_bool($boolean));
        $this->assertTrue($boolean);
    }

    public function testCreate()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $right = new xmlrpcval('create', xmlrpcval::$xmlrpcString);
        $boolean = $client->check_access_rights($this->_model_name, $right);

        $this->assertTrue(is_bool($boolean));
        $this->assertTrue($boolean);
    }
}
