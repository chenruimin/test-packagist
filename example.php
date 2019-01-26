<?php

require_once './src/ClientSideException.php';
require_once './src/Request.php';

$request = new \TestRuimin\Request();

$res = $request->post('users/login', ['username' => 'test', 'password' => 'test']);

echo $res;
