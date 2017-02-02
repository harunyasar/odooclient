<?php

namespace Odoo\Client\Test;

use Odoo\Client\OdooClient;

class StartTest extends TestOdooClient
{
    public function testVersion()
    {
        $client = new OdooClient($this->_host, $this->_port);
        //$start = $client->start();
    }
}
