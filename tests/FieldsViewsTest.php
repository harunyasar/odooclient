<?php namespace Odoo\Client\Test;

use PhpXmlRpc\Value as xmlrpcval;
use Odoo\Client\OdooClient;

class FieldsViewsTest extends TestOdooClient
{
    public function testFieldsGetWithFieldFilter()
    {
        $filter = array(
            new xmlrpcval('name', xmlrpcval::$xmlrpcString),
            new xmlrpcval('code', xmlrpcval::$xmlrpcString),
            new xmlrpcval('phone_code', xmlrpcval::$xmlrpcString)
        );

        $fields = array(new xmlrpcval($filter, xmlrpcval::$xmlrpcArray));

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $res = $client->fields_get($this->_model_name, $fields);

        $this->assertNotNull($res);
        $this->assertTrue(is_array($res));
        $this->assertArrayHasKey('name', $res);
        $this->assertArrayHasKey('code', $res);
        $this->assertArrayHasKey('phone_code', $res);
    }

    public function testFieldsGetWithoutFieldFilter()
    {
        $filter = array();

        $fields = array(new xmlrpcval($filter, xmlrpcval::$xmlrpcArray));

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $res = $client->fields_get($this->_model_name, $fields);

        $this->assertNotNull($res);
        $this->assertTrue(is_array($res));
        $this->assertArrayHasKey('create_uid', $res);
        $this->assertArrayHasKey('code', $res);
        $this->assertArrayHasKey('display_name', $res);
        $this->assertArrayHasKey('name', $res);
        $this->assertArrayHasKey('__last_update', $res);
        $this->assertArrayHasKey('state_ids', $res);
        $this->assertArrayHasKey('image', $res);
        $this->assertArrayHasKey('write_uid', $res);
        $this->assertArrayHasKey('currency_id', $res);
        $this->assertArrayHasKey('address_format', $res);
        $this->assertArrayHasKey('phone_code', $res);
        $this->assertArrayHasKey('country_group_ids', $res);
        $this->assertArrayHasKey('write_date', $res);
        $this->assertArrayHasKey('create_date', $res);
        $this->assertArrayHasKey('id', $res);
    }
}
