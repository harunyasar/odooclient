<?php

namespace Odoo\Client\Test;

use Odoo\Client\OdooClient;

class AuthenticateTest extends TestOdooClient
{
    public function testRealUser()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $uid = $client->getUid();

        $this->assertNotFalse($uid);
        $this->assertGreaterThan(1, $uid);
    }

    public function testFakeUser()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, 'fake', 'fake');
        $uid = $client->getUid();

        $this->assertEquals(0, $uid);
    }

}
