<?php

namespace chebsmix\helpers\curl;

require_once("Curl.php");

$curl = new Curl();

$curl->setUrl('http://localhost')->setMethod("POST");

$curl->printDebug();