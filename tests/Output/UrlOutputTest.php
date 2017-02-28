<?php namespace Odoo\Client\Test\Output;

use Odoo\Client\Output\UrlOutput;
use PHPUnit\Framework\TestCase;

class UrlOutputTest extends TestCase
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
