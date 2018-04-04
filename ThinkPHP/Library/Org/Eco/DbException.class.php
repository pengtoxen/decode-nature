<?php
namespace Org\Eco;

class DbException extends \Exception
{

    protected $SQLSTATE = null;
}