<?php namespace Odoo\Client;

use Odoo\Client\Connection\Connection;
use xmlrpcval;
use xmlrpcmsg;

class OdooClient
{
    /**
     * The endpoint provides meta-calls which don't require authentication,
     * such as the authentication itself or fetching version information.
     * @var string $_common
     */
    private static $_common = '/xmlrpc/common';

    /**
     * The endpoint is used to call methods of odoo models
     * via the execute_kw RPC function.
     * @var string $_object
     */
    private static $_object = '/xmlrpc/object';

    /**
     * Odoo XML-RPC create method
     * @var string $_create
     */
    private static $_create = 'create';

    /**
     * Odoo XML-RPC unlink method
     * @var string $_unlink
     */
    private static $_unlink = 'unlink';

    /**
     * Odoo XML-RPC write method
     * @var string $_write
     */
    private static $_write = 'write';

    /**
     * Odoo XML-RPC search method
     * @var string $_search
     */
    private static $_search = 'search';

    /**
     * Odoo XML-RPC read method
     * @var string $_read
     */
    private static $_read = 'read';

    /**
     * Odoo XML-RPC directly execute custom method
     * @var string $_execute
     */
    private static $_execute = 'execute';

    /**
     * Connection host
     * @var string $_host
     */
    private $_host;

    /**
     * Odoo database name
     * @var string $_db
     */
    private $_db;

    /**
     * Username for login
     * @var string $_username
     */
    private $_username;
    /**
     * Password for login
     * @var string $_password
     */
    private $_password;

    /**
     * Connection port
     * @var string $_port
     */
    private $_port;

    /**
     * The user ID is returned after the login
     * @var string $_uid
     */
    private $_uid;

    /**
     * Connection object
     * @var object $_connection
     */
    private $_connection;

    /**
     * OdooClient constructor
     * @param string $host Connection host
     * @param string $port Connection port
     * @param string $db Odoo database name
     * @param string $username Login username
     * @param string $password Login password
     */
    public function __construct($host, $port, $db, $username, $password)
    {
        $this->_host = $host;
        $this->_port = $port;
        $this->_db = $db;
        $this->_username = $username;
        $this->_password = $password;
        $this->_connection = new Connection($this->_host, $this->_port);
    }

    /**
     * Login with username and password
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception  Throws exception when login fail
     */
    private function _login()
    {
        $message = new xmlrpcmsg('login');

        $message->addParam(new xmlrpcval($this->_db, 'string'));
        $message->addParam(new xmlrpcval($this->_username, 'string'));
        $message->addParam(new xmlrpcval($this->_password, 'string'));

        $response = $this->_connection->create(self::$_common)->send($message);

        if ($response->errno != 0) {
            throw new \Exception($response->faultString());
        }

        return $response;
    }

    /**
     * Retrieves logged user ID
     * @return string     Logged user ID
     * @throws \Exception Throws exception when login fail
     */
    private function _uid()
    {
        $response = $this->_login();

        $this->_uid = $response->value()->scalarval();

        return $this->_uid;
    }

    /**
     * Message creator for XML-RPC request
     * @return xmlrpcmsg
     */
    private function _execute()
    {
        $execute = new xmlrpcmsg('execute');

        $execute->addParam(new xmlrpcval($this->_db, 'string'));
        $execute->addParam(new xmlrpcval($this->_uid(), 'int'));
        $execute->addParam(new xmlrpcval($this->_password, 'string'));

        return $execute;
    }

    /**
     * Odoo XML-RPC create method
     * @param string $model Odoo model name
     * @param array $data Request input data
     * @return \xmlrpcresp
     */
    public function create($model, $data)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, 'string'));
        $msg->addParam(new xmlrpcval(self::$_create, 'string'));
        $msg->addParam(new xmlrpcval($data, 'struct'));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $response;
    }

    /**
     * Odoo XML-RPC search method
     * @param string $model Odoo model name
     * @param array $domain Domain filter array
     * @return \xmlrpcresp
     */
    public function search($model, $domain)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, 'string'));
        $msg->addParam(new xmlrpcval(self::$_search, 'string'));
        $msg->addParam(new xmlrpcval($domain, 'array'));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $response;
    }

    /**
     * Odoo XML-RPC read method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @param array $fields Fields of data
     * @return \xmlrpcresp
     */
    public function read($model, $ids, $fields)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, 'string'));
        $msg->addParam(new xmlrpcval(self::$_read, 'string'));
        $msg->addParam(new xmlrpcval($ids, 'array'));
        $msg->addParam(new xmlrpcval($fields, 'array'));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $response;
    }

    /**
     * Odoo XML-RPC unlink method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @return \xmlrpcresp
     */
    public function unlink($model, $ids)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, 'string'));
        $msg->addParam(new xmlrpcval(self::$_unlink, 'string'));
        $msg->addParam(new xmlrpcval($ids, 'array'));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $response;
    }

    /**
     * Odoo XML-RPC write method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @param array $values New values
     * @return \xmlrpcresp
     */
    public function write($model, $ids, $values)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, 'string'));
        $msg->addParam(new xmlrpcval(self::$_write, 'string'));
        $msg->addParam(new xmlrpcval($ids, 'array'));
        $msg->addParam(new xmlrpcval($values, 'struct'));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $response;
    }

    /**
     * @param $model
     * @param $method
     * @param $data
     * @return mixed
     */

    /**
     * Odoo XML-RPC execute method
     * @param string $model Odoo model name
     * @param string $method Custom method
     * @param array $data Request input data
     * @return \xmlrpcresp
     */
    public function execute($model, $method, $data)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, 'string'));
        $msg->addParam(new xmlrpcval($method, 'string'));
        $msg->addParam(new xmlrpcval($data, 'struct'));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $response;
    }
}