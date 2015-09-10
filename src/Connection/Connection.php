<?php namespace Odoo\Client\Connection;

use Odoo\Client\Output\UrlOutput;
use xmlrpc_client;

class Connection implements ConnectionInterface
{
    /**
     * Common or object type of Odoo connection
     * @var string
     */
    private $_type;

    /**
     * Odoo client object
     * @var object
     */
    private $_client;

    /**
     * Connection host
     * @var string
     */
    private $_host;

    /**
     * Connection port
     * @var string
     */
    private $_port;

    /**
     * Connection url
     * @var string
     */
    private $_url;

    /**
     * Class constructor
     * @param string $host Odoo URL
     * @param string $port Odoo URL port
     */
    public function __construct($host, $port)
    {
        $this->_host = $host;
        $this->_port = $port;
        $this->_url = new UrlOutput();
    }

    /**
     * Creating connection for client with given type parameter
     * @param  null $type Common or object type for connection
     * @return object|xmlrpc_client
     */
    public function create($type = null)
    {
        if ($this->_type === $type) {
            return $this->_client;
        }

        $this->_type = $type;

        $url = $this->_url->createUrl($this->_host, $this->_port, $type);

        $this->_client = new xmlrpc_client($url);
        $this->_client->setSSLVerifyPeer(0);
        return $this->_client;
    }
}