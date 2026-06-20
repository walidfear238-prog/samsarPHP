<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


define('PROPERTY_IMG_DIR', 'uploads/property_images/');

require "../../db/connect.php";







?>