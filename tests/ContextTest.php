<?php

namespace Odoo\Client\Test;

use Odoo\Client\OdooClient;

class ContextTest extends TestOdooClient
{
    public function testRealUser()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $context = $client->context_get();

        $this->assertNotFalse($context);
        $this->assertGreaterThan(1, count($context));
        $this->assertArrayHasKey('lang', $context);
        $this->assertArrayHasKey('tz', $context);
    }

}
