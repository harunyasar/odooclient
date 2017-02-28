<?php
namespace Odoo\Client\Test;

use PHPUnit\Framework\TestCase;

class TestOdooClient extends TestCase
{
    protected $_host = 'https://odoophpclient.odoo.com';

    protected $_port = 443;

    protected $_db = 'odoophpclient';

    protected $_username = 'test@odoophpclient.odoo.com';

    protected $_password = 'odoophpclient';

    protected $_model_name = 'res.country';
}
