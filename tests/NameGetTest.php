<?php
namespace Odoo\Client\Test;

use PhpXmlRpc\Value as xmlrpcval;
use Odoo\Client\OdooClient;

class NameGetTest extends TestOdooClient
{
    public function testOneId()
    {
        $ids = new xmlrpcval(1, xmlrpcval::$xmlrpcInt);
        $ids = array($ids);

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $result = $client->name_get($this->_model_name, $ids);

        $this->assertTrue(is_array($result));
        $this->assertEquals(count($ids), count($result));
    }

    public function testContext()
    {
        $ids = new xmlrpcval(1, xmlrpcval::$xmlrpcInt);
        $ids = array($ids);

        $context = array(
            'lang' => new xmlrpcval('fr_FR', xmlrpcval::$xmlrpcString),
            'tz' => new xmlrpcval('Europe/Paris', xmlrpcval::$xmlrpcString)
        );
        $parameters = array(
            'context' => new xmlrpcval($context, xmlrpcval::$xmlrpcStruct)
        );

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $result = $client->name_get($this->_model_name, $ids, $parameters);

        $this->assertTrue(is_array($result));
        $this->assertEquals(count($ids), count($result));
        $this->assertArrayHasKey(0, $result);

        $country = $result[0];
        $this->assertTrue(is_array($country));
        $this->assertArrayNotHasKey('image', $country);
        $this->assertArrayHasKey(1, $country);
        $this->assertEquals($country[1], 'Principauté d\'Andorre');
    }

    public function testTwoIds()
    {
        $myIds = array(
            new xmlrpcval(1, xmlrpcval::$xmlrpcInt),
            new xmlrpcval(2, xmlrpcval::$xmlrpcInt)
        );
        $ids = new xmlrpcval($myIds, xmlrpcval::$xmlrpcArray);
        $ids = array($ids);

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $result = $client->name_get($this->_model_name, $ids);

        $this->assertTrue(is_array($result));
        $this->assertEquals(count($myIds), count($result));
    }

}
