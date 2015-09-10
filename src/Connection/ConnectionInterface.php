<?php namespace Odoo\Client\Connection;

interface ConnectionInterface
{
    /**
     * @param  string $type Common or object type of Odoo connection
     * @return mixed
     */
    public function create($type);
}