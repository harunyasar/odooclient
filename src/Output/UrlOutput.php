<?php namespace Odoo\Client\Output;

use Odoo\Client\Output\UrlOutputInterface;

class UrlOutput implements UrlOutputInterface {
    /**
     * Formatting URL for connection
     * @param string $host Connection host
     * @param string $port Connection port
     * @param string $type Common or object type for connection
     * @return string
     */
    public function createUrl($host, $port, $type)
    {
        return $host . ':' . $port . $type;
    }
}