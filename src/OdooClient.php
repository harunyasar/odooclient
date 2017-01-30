<?php namespace Odoo\Client;

use Odoo\Client\Connection\Connection;
use PhpXmlRpc\Value as xmlrpcval;
use PhpXmlRpc\Request as xmlrpcmsg;
use PhpXmlRpc\Response as xmlrpcresp;

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
     * Odoo XML-RPC context_get method
     * @var string $_context_get
     */
    private static $_context_get = 'context_get';

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
     * Odoo XML-RPC search_read method
     * @var string $_search_read
     */
    private static $_search_read = 'search_read';

    /**
     * Odoo XML-RPC name_get method
     * @var string $_name_get
     */
    private static $_name_get = 'name_get';

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
     * @param int $port Connection port
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
     * Version of Odoo
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception
     */
    public function version()
    {
        $message = new xmlrpcmsg('version');

        $response = $this->_connection->create(self::$_common)->send($message);

        return $this->_response($response);
    }

    /**
     * Login with username and password
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception  Throws exception when login fail
     */
    private function _login()
    {
        $message = new xmlrpcmsg('login');

        $message->addParam(new xmlrpcval($this->_db, xmlrpcval::$xmlrpcString));
        $message->addParam(new xmlrpcval($this->_username, xmlrpcval::$xmlrpcString));
        $message->addParam(new xmlrpcval($this->_password, xmlrpcval::$xmlrpcString));

        $response = $this->_connection->create(self::$_common)->send($message);

        return $this->_response($response);
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
     * Public method to retrieves logged user ID
     * @return string     Logged user ID
     * @throws \Exception Throws exception when login fail
     */
    public function getUid()
    {
        return $this->_uid();
    }

    /**
     * Message creator for XML-RPC request
     * @return xmlrpcmsg
     */
    private function _execute()
    {
        $msg = new xmlrpcmsg(self::$_execute);

        $msg->addParam(new xmlrpcval($this->_db, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($this->_uid(), xmlrpcval::$xmlrpcInt));
        $msg->addParam(new xmlrpcval($this->_password, xmlrpcval::$xmlrpcString));

        return $msg;
    }

    /**
     * Odoo XML-RPC context_get method of logged user
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception
     */
    public function context_get()
    {
        $execute = new xmlrpcmsg(self::$_execute);

        $execute->addParam(new xmlrpcval($this->_db, xmlrpcval::$xmlrpcString));
        $execute->addParam(new xmlrpcval($this->_uid(), xmlrpcval::$xmlrpcInt));
        $execute->addParam(new xmlrpcval($this->_password, xmlrpcval::$xmlrpcString));
        $execute->addParam(new xmlrpcval('res.users', xmlrpcval::$xmlrpcString));
        $execute->addParam(new xmlrpcval(self::$_context_get, xmlrpcval::$xmlrpcString));

        $response = $this->_connection->create(self::$_object)->send($execute);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC create method
     * @param string $model Odoo model name
     * @param array $data Request input data
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception
     */
    public function create($model, $data)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_create, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($data, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC search method
     * @param string $model Odoo model name
     * @param array $domain Domain filter array
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception
     */
    public function search($model, $domain, xmlrpcval $context = NULL, $fields = NULL)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_search, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($domain, xmlrpcval::$xmlrpcArray));

        if (!is_null($fields))
            $msg->addParam(new xmlrpcval($fields, xmlrpcval::$xmlrpcArray));

        if (!is_null($context))
            $msg->addParam(new xmlrpcval($context, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC read method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @param array $fields Fields of data
     * @param null $params
     * @return xmlrpcresp
     * @throws \Exception
     */
    public function read($model, $ids, xmlrpcval $context = NULL, $fields = NULL)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_read, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($ids, xmlrpcval::$xmlrpcArray));

        if (!is_null($fields))
            $msg->addParam(new xmlrpcval($fields, xmlrpcval::$xmlrpcArray));

        if (!is_null($context))
            $msg->addParam(new xmlrpcval($context, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC search_read method
     * @param string $model Odoo model name
     * @param array $domain Domain filter array
     * @param array $fields Fields of data
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception
     */
    public function search_read($model, $domain, xmlrpcval $context = NULL, $fields = NULL)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_search_read, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($domain, xmlrpcval::$xmlrpcArray));

        if (!is_null($fields))
            $msg->addParam(new xmlrpcval($fields, xmlrpcval::$xmlrpcArray));

        if (!is_null($context))
            $msg->addParam(new xmlrpcval($context, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC name_get method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @return xmlrpcresp|\PhpXmlRpc\Response[]
     * @throws \Exception
     */
    public function name_get($model, $ids, xmlrpcval $context = NULL)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_name_get, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($ids, xmlrpcval::$xmlrpcArray));

        if (!is_null($context))
            $msg->addParam(new xmlrpcval($context, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC unlink method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception
     */
    public function unlink($model, $ids)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_unlink, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($ids, xmlrpcval::$xmlrpcArray));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC write method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @param array $values New values
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception
     */
    public function write($model, $ids, $values)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_write, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($ids, xmlrpcval::$xmlrpcArray));
        $msg->addParam(new xmlrpcval($values, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC execute method
     * @param string $model Odoo model name
     * @param string $method Custom method
     * @param array $data Request input data
     * @return \xmlrpcresp Odoo XML-RPC reponse
     * @throws \Exception
     */
    public function execute($model, $method, $data)
    {
        $msg = $this->_execute();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($method, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($data, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);

        return $this->_response($response);
    }

    /**
     * Odoo XML-RPC return response
     * @param xmlrpcresp $response
     * @return xmlrpcresp
     * @throws \Exception
     */
    private function _response(xmlrpcresp $response)
    {
        if ($response->errno != 0) {
            throw new \Exception($response->faultString());
        }

        return $response;
    }

}
