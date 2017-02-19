<?php namespace Odoo\Client\Test\Output;

use Odoo\Client\Output\UrlOutput;

class UrlOutPutTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateUrl()
    {
        $urlOutput = new UrlOutput();

        $host = 'test.example.com';
        $port = 8069;
        $type = null;

        $output = $urlOutput->createUrl($host, $port, $type);

        $this->assertSame('test.example.com:8069', $output);
    }
}
