<?php namespace Odoo\Client\Test\Output;

use Odoo\Client\Output\UrlOutput;

class UrlOutputTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateUrl()
    {
        $urlOutput = new UrlOutput();

        $host = 'test.example.com';
        $port = 8069;
        $type = null;
        $expected = 'test.example.com:8069';

        $output = $urlOutput->createUrl($host, $port, $type);

        $this->assertSame($expected, $output);
    }
}
