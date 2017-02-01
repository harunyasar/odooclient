<?php

namespace Odoo\Client\Test;

use Odoo\Client\OdooClient;

class VersionTest extends TestOdooClient
{
    public function testVersion()
    {
        $minimumVersionNumber = 8;

        $client = new OdooClient($this->_host, $this->_port);
        $version = $client->version();

        $key = 'server_serie';
        $this->assertArrayHasKey(
            $key,
            $version,
            "Key \"$key\" not found."
        );
        $this->assertGreaterThan(
            $minimumVersionNumber,
            $version[$key],
            "Odoo version under $minimumVersionNumber."
        );

        $key = 'server_version_info';
        $this->assertArrayHasKey(
            $key,
            $version,
            "Key \"$key\" not found."
        );
        $this->assertArrayHasKey(
            0,
            $version[$key],
            "Key \"0\" not found."
        );
        $this->assertGreaterThan(
            $minimumVersionNumber,
            $version[$key][0],
            "Odoo version under $minimumVersionNumber"
        );
    }
}
