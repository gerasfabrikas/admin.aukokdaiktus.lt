<?php
//if ($_SERVER['REMOTE_ADDR'] != '89.190.113.13') die('up');

/**
 * A simple, clean and secure PHP Login Script
 *
 * ADVANCED VERSION
 * (check the website / GitHub / facebook for other versions)
 *
 * A simple PHP Login Script.
 * Uses PHP SESSIONS, modern password-hashing and salting
 * and gives the basic functions a proper login system needs.
 *
 * @package php-login
 * @author Panique
 * @link https://github.com/panique/php-login/
 * @license http://opensource.org/licenses/MIT MIT License
 */
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'lt';
// load php-login components
require_once("php-login.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process.
$login = new Login();
include('functions.php');
// ... ask if we are logged in here:
include("views/site.php");
