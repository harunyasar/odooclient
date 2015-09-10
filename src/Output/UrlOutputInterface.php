<?php namespace Odoo\Client\Output;

interface UrlOutputInterface {
    /**
     * Formatting URL for connection
     * @param string $host Connection host
     * @param string $port Connection port
     * @param string $type Common or object type for connection
     * @return mixed
     */
    public function createUrl($host, $port, $type);
}