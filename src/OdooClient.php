<?php namespace Odoo\Client;

use Odoo\Client\Connection\Connection;
use Odoo\Client\Transformer\Transformer;
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
    private static $_common = '/xmlrpc/2/common';

    /**
     * The endpoint is used to call methods of Odoo models
     * via the execute_kw RPC function.
     * @var string $_object
     */
    private static $_object = '/xmlrpc/2/object';

    /**
     * Odoo XML-RPC directly execute custom method
     * @var string $_execute
     */
    private static $_execute = 'execute_kw';

    /**
     * res.user model
     * @var string $_user_model
     */
    private static $_user_model = 'res.users';

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
     * Odoo XML-RPC search_count method
     * @var string $_search_count
     */
    private static $_search_count = 'search_count';

    /**
     * Odoo XML-RPC name_get method
     * @var string $_name_get
     */
    private static $_name_get = 'name_get';

    /**
     * Odoo XML-RPC fields_get method
     * @var string $_fields_get
     */
    private static $_fields_get = 'fields_get';

    /**
     * Odoo XML-RPC check_access_rights method
     * @var string $_check_access_rights
     */
    private static $_check_access_rights = 'check_access_rights';

    /**
     * Connection host
     * @var string $_host
     */
    private $_host;

    /**
     * Connection port
     * @var int $_port
     */
    private $_port;

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
     * The user ID is returned after the login
     * @var int $_uid
     */
    private $_uid;

    /**
     * Connection object
     * @var object $_connection
     */
    private $_connection;

    /**
     * Response transform
     * @var Transformer $_transform
     */
    private $_transform;

    /**
     * OdooClient constructor
     * @param string $host Connection host
     * @param int $port Connection port
     * @param string $db Odoo database name
     * @param string $username Login username
     * @param string $password Login password
     */
    public function __construct($host, $port, $db = NULL, $username = NULL, $password = NULL)
    {
        $this->_host = $host;
        $this->_port = $port;

        $this->_db = $db;
        $this->_username = $username;
        $this->_password = $password;

        $this->_connection = new Connection($this->_host, $this->_port);
        $this->_transform = new Transformer();
    }

    /**
     * Start page of Odoo
     * @return xmlrpcresp|\PhpXmlRpc\Response[]  Odoo XML-RPC response
     * @throws \Exception
     */
    public function start()
    {
        $message = new xmlrpcmsg('start');

        $response = $this->_connection->create('/start')->send($message);
        $response = $this->_checkResponse($response);

        return $response;
    }

    /**
     * Version of Odoo
     * @return array|xmlrpcresp|\PhpXmlRpc\Response[]  Odoo XML-RPC response
     * @throws \Exception
     */
    public function version()
    {
        $message = new xmlrpcmsg('version');

        $response = $this->_connection->create(self::$_common)->send($message);
        $response = $this->_checkResponse($response);
        $response = $this->_transform->toArray($response);

        return $response;
    }

    /**
     * Login with username and password
     * @return xmlrpcresp Odoo XML-RPC response
     * @throws \Exception Throws exception when login fail
     */
    private function _login()
    {
        $message = new xmlrpcmsg('login');

        $message->addParam(new xmlrpcval($this->_db, xmlrpcval::$xmlrpcString));
        $message->addParam(new xmlrpcval($this->_username, xmlrpcval::$xmlrpcString));
        $message->addParam(new xmlrpcval($this->_password, xmlrpcval::$xmlrpcString));

        $response = $this->_connection->create(self::$_common)->send($message);

        return $this->_checkResponse($response);
    }

    /**
     * Retrieves logged user ID
     * @return int Logged user ID
     * @throws \Exception Throws exception when login fail
     */
    private function _uid()
    {
        $response = $this->_login();

        $this->_uid = (int) $response->value()->scalarval();

        return $this->_uid;
    }

    /**
     * Public method to retrieves logged user ID
     * @return int Logged user ID
     * @throws \Exception Throws exception when login fail
     */
    public function getUid()
    {
        return $this->_uid();
    }

    /**
     * Odoo XML-RPC context_get method of logged user
     * @param array $parameters Parameters as context, fields, etc
     * @return array|xmlrpcresp|\PhpXmlRpc\Response[] Odoo XML-RPC reponse
     */
    public function context_get(array $parameters = array())
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval(self::$_user_model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_context_get, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($parameters, xmlrpcval::$xmlrpcArray));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);
        $response = $this->_transform->toArray($response);

        return $response;
    }

    /**
     * Odoo XML-RPC create method
     * @param string $model Odoo model name
     * @param array $data Request input data
     * @return bool|int Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    public function create($model, array $data)
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_create, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($data, xmlrpcval::$xmlrpcArray));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);
        $response = $this->_transform->toArray($response);

        // Extract new ID inserted
        $response = (isset($response['int']) ? (int) $response['int'] : FALSE);

        return $response;
    }

    /**
     * Odoo XML-RPC search method
     * @param string $model Odoo model name
     * @param array $domain Domain filter array
     * @param array $parameters Parameters as context, fields, etc
     * @return array|xmlrpcresp|\PhpXmlRpc\Response[] Odoo XML-RPC response
     * @throws \Exception
     */
    public function search($model, array $domain, array $parameters = array())
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_search, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($domain, xmlrpcval::$xmlrpcArray));
        $msg->addParam(new xmlrpcval($parameters, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);
        $response = $this->_transform->toArray($response);

        return $response;
    }

    /**
     * Odoo XML-RPC check access rights method
     * @param string $model Odoo model name
     * @param string $right access to check
     * @return boolean Odoo XML-RPC response
     * @throws \Exception
     */
    public function check_access_rights($model, $right)
    {
        // raise_exception passed by keyword (in order to get a true/false result rather than true/error)
        $parameters = array(
            'raise_exception' => new xmlrpcval(FALSE, xmlrpcval::$xmlrpcBoolean)
        );

        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_check_access_rights, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(array($right), xmlrpcval::$xmlrpcArray));
        $msg->addParam(new xmlrpcval($parameters, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);
        $response = $response->value()->scalarval();

        return $response;
    }

    /**
     * Odoo XML-RPC read method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @param array $parameters Parameters as context, fields, etc
     * @return array|xmlrpcresp|\PhpXmlRpc\Response[] Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    public function read($model, array $ids, array $parameters = array())
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_read, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($ids, xmlrpcval::$xmlrpcArray));
        $msg->addParam(new xmlrpcval($parameters, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);
        $response = $this->_transform->toArray($response);

        return $response;
    }

    /**
     * Odoo XML-RPC search_read method
     * @param string $model Odoo model name
     * @param array $domain Domain filter
     * @param array $parameters Parameters as context, fields, etc
     * @return array|xmlrpcresp|\PhpXmlRpc\Response[] Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    public function search_read($model, array $domain, array $parameters = array())
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_search_read, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($domain, xmlrpcval::$xmlrpcArray));
        $msg->addParam(new xmlrpcval($parameters, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);
        $response = $this->_transform->toArray($response);

        return $response;
    }

    /**
     * Returns the number of records in the current model matching the provided domain.
     * @param string $model Odoo model name
     * @param array $domain Domain filter array
     * @return array|xmlrpcresp|\PhpXmlRpc\Response[] Odoo XML-RPC response
     * @throws \Exception
     */
    public function search_count($model, array $domain)
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_search_count, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($domain, xmlrpcval::$xmlrpcArray));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);

        return $response->value()->scalarval();
    }

    /**
     * Odoo XML-RPC name_get method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @param array $parameters Parameters as context, fields, etc
     * @return array|xmlrpcresp|\PhpXmlRpc\Response[] Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    public function name_get($model, array $ids, array $parameters = array())
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_name_get, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($ids, xmlrpcval::$xmlrpcArray));
        $msg->addParam(new xmlrpcval($parameters, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);
        $response = $this->_transform->toArray($response);

        return $response;
    }

    /**
     * Return the definition of each field.
     * @param string $model Odoo model name
     * @param array $fields list of fields to document, all if empty or not provided
     * @param array $attributes list of description attributes to return for each field, all if empty or not provided
     * @return array|xmlrpcresp|\PhpXmlRpc\Response[] Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    public function fields_get($model, array $fields, array $attributes = array())
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_fields_get, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($fields, xmlrpcval::$xmlrpcArray));
        $msg->addParam(new xmlrpcval($attributes, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);
        $response = $this->_transform->toArray($response);

        return $response;
    }

    /**
     * Odoo XML-RPC unlink method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @return bool Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    public function unlink($model, array $ids)
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_unlink, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($ids, xmlrpcval::$xmlrpcArray));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);

        // Extract response
        $response = (int) $response->value()->scalarval();

        return ($response === 1 ? TRUE : FALSE);
    }

    /**
     * Odoo XML-RPC write method
     * @param string $model Odoo model name
     * @param array $ids Data IDs
     * @param array $values New values
     * @return bool Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    public function write($model, array $ids, array $values)
    {
        // format array which contains values
        $values = array('vals' => new xmlrpcval($values, xmlrpcval::$xmlrpcStruct));

        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval(self::$_write, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($ids, xmlrpcval::$xmlrpcArray));
        $msg->addParam(new xmlrpcval($values, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);

        // Extract response
        $response = (int) $response->value()->scalarval();

        return ($response === 1 ? TRUE : FALSE);
    }

    /**
     * Odoo XML-RPC execute method
     * @param string $model Odoo model name
     * @param string $method Custom method
     * @param array $data Request input data
     * @return xmlrpcresp|\PhpXmlRpc\Response[] Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    public function execute($model, $method, array $data)
    {
        $msg = $this->_createMessageHeader();
        $msg->addParam(new xmlrpcval($model, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($method, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($data, xmlrpcval::$xmlrpcStruct));

        $response = $this->_connection->create(self::$_object)->send($msg);
        $response = $this->_checkResponse($response);

        return $response;
    }

    /**
     * Message creator for XML-RPC request
     * @return xmlrpcmsg Message header
     */
    private function _createMessageHeader()
    {
        $msg = new xmlrpcmsg(self::$_execute);

        $msg->addParam(new xmlrpcval($this->_db, xmlrpcval::$xmlrpcString));
        $msg->addParam(new xmlrpcval($this->_uid(), xmlrpcval::$xmlrpcInt));
        $msg->addParam(new xmlrpcval($this->_password, xmlrpcval::$xmlrpcString));

        return $msg;
    }

    /**
     * Odoo XML-RPC return response
     * @param xmlrpcresp $response
     * @return xmlrpcresp Odoo XML-RPC response
     * @throws \Exception Throws exception when request fail
     */
    private function _checkResponse(xmlrpcresp $response)
    {
        if ($response->errno != 0) {
            throw new \Exception($response->faultString());
        }

        return $response;
    }

}
