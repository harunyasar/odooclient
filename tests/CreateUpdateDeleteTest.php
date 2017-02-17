<?php

namespace Odoo\Client\Test;

use Odoo\Client\OdooClient;
use PhpXmlRpc\Value;

class CreateUpdateDeleteTest extends TestOdooClient
{
    private $_modelName = 'res.country.state';
    private $_countryIdTurkey = 226;
    private $_stateCode = 'STA';
    private $_originalStateName = 'İstanbul, (Stambul)';
    private $_updatedStateName = 'İstanbul';

    public function testMyScenario()
    {
        $id = $this->_createCountryState();

        if ($afterCreation = $this->_searchCountryState()) {
            $this->assertTrue(is_array($afterCreation));
            $this->assertArrayHasKey(0, $afterCreation);
            $this->assertTrue(is_int($afterCreation[0]));
            $this->assertEquals($afterCreation[0], $id);
        }

        $this->_updateCountryState(array($id));

        if ($afterUpdate = $this->_searchCountryState('search_read')) {
            $this->assertTrue(is_array($afterUpdate));
            $this->assertArrayHasKey(0, $afterUpdate);
            $this->assertTrue(is_array($afterUpdate[0]) && !empty($afterUpdate[0]));
            $this->assertArrayHasKey('display_name', $afterUpdate[0]);
            $this->assertTrue(is_string($afterUpdate[0]['display_name']));
            $this->assertEquals($afterUpdate[0]['display_name'], $this->_updatedStateName);
        }

        $this->_deleteCountryState(array($id));

        if ($afterDelete = $this->_searchCountryState()) {
            $this->assertTrue(is_array($afterDelete));
            $this->assertTrue(empty($afterDelete));
        }
    }

    private function _createCountryState()
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);

        $context = $client->context_get();
        array_walk($context, function(&$item) {
            $item = new Value($item, Value::$xmlrpcString);
        });

        $data =
            array(
                new Value(array(
                    'code' => new Value($this->_stateCode, Value::$xmlrpcString),
                    'name' => new Value($this->_originalStateName, Value::$xmlrpcString),
                    'country_id' => new Value($this->_countryIdTurkey, Value::$xmlrpcInt)
                ), Value::$xmlrpcStruct),
                new Value(array(), Value::$xmlrpcStruct)
            )
        ;

        $create = $client->create($this->_modelName, $data);

        $this->assertNotFalse($create);
        $this->assertTrue(is_int($create));
        $this->assertTrue($create > 0);

        return $create;
    }

    private function _deleteCountryState(array $ids)
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);

        array_walk($ids, function(&$item) {
            $item = new Value($item, Value::$xmlrpcInt);
        });
        $ids = array(
            new Value($ids, Value::$xmlrpcArray)
        );

        $unlink = $client->unlink($this->_modelName, $ids);

        $this->assertTrue($unlink);
    }

    private function _updateCountryState(array $ids)
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);

        array_walk($ids, function(&$item) {
            $item = new Value($item, Value::$xmlrpcInt);
        });
        $ids = array(
            new Value($ids, Value::$xmlrpcArray)
        );

        $data = array('name' => new Value($this->_updatedStateName, Value::$xmlrpcString));

        $write = $client->write($this->_modelName, $ids, $data);

        $this->assertTrue($write);
    }

    private function _searchCountryState($method = 'search')
    {
        $client = new OdooClient($this->_host, $this->_port, $this->_db, $this->_username, $this->_password);

        $domain = array(
            new Value(array(
                new Value(array(
                    new Value('country_id', Value::$xmlrpcString),
                    new Value('=', Value::$xmlrpcString),
                    new Value($this->_countryIdTurkey, Value::$xmlrpcInt)
                ), Value::$xmlrpcArray),
                new Value(array(
                    new Value('code', Value::$xmlrpcString),
                    new Value('=', Value::$xmlrpcString),
                    new Value($this->_stateCode, Value::$xmlrpcString)
                ), Value::$xmlrpcArray)
            ), Value::$xmlrpcArray)
        );

        if ($method == 'search_read')
            $result = $client->search_read($this->_modelName, $domain);
        else
            $result = $client->search($this->_modelName, $domain);

        return $result;
    }

}
