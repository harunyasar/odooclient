<?php
namespace Odoo\Client\Test;

use PhpXmlRpc\Value as xmlrpcval;
use Odoo\Client\OdooClient;

class ReadTest extends TestOdooClient
{
    public function testOneId()
    {
        $ids = new xmlrpcval(1, xmlrpcval::$xmlrpcInt);
        $ids = array($ids);

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $result = $client->read($this->_model_name, $ids);

        $this->assertTrue(is_array($result));
        $this->assertEquals(count($ids), count($result));
    }

    public function testFields()
    {
        $ids = new xmlrpcval(1, xmlrpcval::$xmlrpcInt);
        $ids = array($ids);

        $fields = array(
            new xmlrpcval('id', xmlrpcval::$xmlrpcString),
            new xmlrpcval('code', xmlrpcval::$xmlrpcString),
            new xmlrpcval('name', xmlrpcval::$xmlrpcString)
        );
        $parameters = array(
            'fields' => new xmlrpcval($fields, xmlrpcval::$xmlrpcArray)
        );

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $result = $client->read($this->_model_name, $ids, $parameters);

        $this->assertTrue(is_array($result));
        $this->assertEquals(count($ids), count($result));
        $this->assertArrayHasKey(0, $result);

        $country = $result[0];

        $this->assertTrue(is_array($country));
        $this->assertEquals(count($fields), count($country));
        $this->assertArrayNotHasKey('image', $country);
        $this->assertArrayHasKey('name', $country);
        $this->assertEquals($country['name'], 'Andorra, Principality of');
    }

    public function testContext()
    {
        $ids = new xmlrpcval(1, xmlrpcval::$xmlrpcInt);
        $ids = array($ids);

        $context = array(
            'lang' => new xmlrpcval('fr_FR', xmlrpcval::$xmlrpcString),
            'tz' => new xmlrpcval('Europe/Paris', xmlrpcval::$xmlrpcString)
        );
        $fields = array(
            new xmlrpcval('id', xmlrpcval::$xmlrpcString),
            new xmlrpcval('code', xmlrpcval::$xmlrpcString),
            new xmlrpcval('name', xmlrpcval::$xmlrpcString)
        );
        $parameters = array(
            'fields' => new xmlrpcval($fields, xmlrpcval::$xmlrpcArray),
            'context' => new xmlrpcval($context, xmlrpcval::$xmlrpcStruct)
        );

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $result = $client->read($this->_model_name, $ids, $parameters);

        $this->assertTrue(is_array($result));
        $this->assertEquals(count($ids), count($result));
        $this->assertArrayHasKey(0, $result);

        $country = $result[0];
        $this->assertTrue(is_array($country));
        $this->assertEquals(count($fields), count($country));
        $this->assertArrayNotHasKey('image', $country);
        $this->assertArrayHasKey('name', $country);
        $this->assertEquals($country['name'], 'Principauté d\'Andorre');
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
        $result = $client->read($this->_model_name, $ids);

        $this->assertTrue(is_array($result));
        $this->assertEquals(count($myIds), count($result));
    }




    /*
    public function testPagination()
    {
        $limit = 5;

        $parameters = array(
            'offset' => new xmlrpcval(10, xmlrpcval::$xmlrpcInt),
            'limit' => new xmlrpcval($limit, xmlrpcval::$xmlrpcInt),
        );

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $domain = array(new xmlrpcval(array(), xmlrpcval::$xmlrpcArray));
        $ids = $client->search($this->_model_name, $domain, $parameters);

        $this->assertTrue(is_array($ids));
        $this->assertEquals($limit, count($ids));
    }

    public function testFilter()
    {
        $restriction = array(
            new xmlrpcval('code', xmlrpcval::$xmlrpcString),
            new xmlrpcval('=', xmlrpcval::$xmlrpcString),
            new xmlrpcval('US', xmlrpcval::$xmlrpcString)
        );
        $domain = array(new xmlrpcval($restriction, xmlrpcval::$xmlrpcArray));

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $domain = array(new xmlrpcval($domain, xmlrpcval::$xmlrpcArray));
        $ids = $client->search($this->_model_name, $domain);

        $this->assertTrue(is_array($ids));
        $this->assertEquals(1, count($ids));
        $this->assertArrayHasKey(0, $ids);
        $this->assertEquals($ids[0], 235);
    }
*/
}
