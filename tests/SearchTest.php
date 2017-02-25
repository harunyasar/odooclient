<?php
namespace Odoo\Client\Test;

use PhpXmlRpc\Value as xmlrpcval;
use Odoo\Client\OdooClient;

class SearchTest extends TestOdooClient
{
    public function testAll()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);
        $domain = array(new xmlrpcval(array(), xmlrpcval::$xmlrpcArray));
        $ids = $client->search($this->_model_name, $domain);

        $this->assertTrue(is_array($ids));
        $this->assertGreaterThan(200, count($ids));
    }

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

    public function testSearchCountWithDomainFilter()
    {
        $domain = array(
            new xmlrpcval('code', xmlrpcval::$xmlrpcString),
            new xmlrpcval('=', xmlrpcval::$xmlrpcString),
            new xmlrpcval('US', xmlrpcval::$xmlrpcString)
        );

        $filter = array(new xmlrpcval($domain, xmlrpcval::$xmlrpcArray));

        $domainFilter = array(new xmlrpcval($filter, xmlrpcval::$xmlrpcArray));

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);

        $count = $client->search_count($this->_model_name, $domainFilter);

        $this->assertEquals($count, 1);
    }

    public function testSearchCountWithoutDomainFilter()
    {
        $filter = array();

        $domainFilter = array(new xmlrpcval($filter, xmlrpcval::$xmlrpcArray));

        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);

        $count = $client->search_count($this->_model_name, $domainFilter);

        $this->assertEquals($count, 253);
    }
}
